<?php

namespace Cloudz\Strategy;

use Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Cloudz\CloudServiceFile;
use Cloudz\CloudServiceSettings;
use Cloudz\DeleteCloudServiceFile;
use Cloudz\GoogleDrive\GoogleDriveAccount;
use Cloudz\Strategy\CloudServiceStrategy;

class GoogleDriveStrategy extends CloudServiceStrategy
{
    private GoogleDriveAccount $googleDriveAccount;
    private Google_Client $client;
    private Google_Service_Drive $service;

    public function __construct(GoogleDriveAccount $googleDriveAccount, CloudServiceSettings $settings)
    {
        parent::__construct($settings);
        $this->googleDriveAccount = $googleDriveAccount;
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $this->client = new Google_Client();
        $this->client->setClientId($this->googleDriveAccount->clientId);
        $this->client->setClientSecret($this->googleDriveAccount->clientSecret);
        $this->client->setAccessType('offline');
        $this->client->addScope(Google_Service_Drive::DRIVE_FILE);
        
        if (!empty($this->googleDriveAccount->refreshToken)) {
            $this->client->refreshToken($this->googleDriveAccount->refreshToken);
        } elseif (!empty($this->googleDriveAccount->accessToken)) {
            $this->client->setAccessToken($this->googleDriveAccount->accessToken);
        }
        
        $this->service = new Google_Service_Drive($this->client);
    }

    protected function beforeExecute()
    {
        if (!$this->client->getAccessToken()) {
            throw new Exception('Token de acesso do Google Drive não configurado ou expirado.', 401);
        }
        
        if ($this->client->isAccessTokenExpired()) {
            if (!empty($this->googleDriveAccount->refreshToken)) {
                $this->client->refreshToken($this->googleDriveAccount->refreshToken);
            } else {
                throw new Exception('Token de acesso expirado e refresh token não disponível.', 401);
            }
        }
    }

    protected function doUpload(CloudServiceFile $file)
    {
        $remoteFileName = $file->getRemoteFileName($this->settings->get('canEncryptName', false));
        $localFilePath = $file->getLocalFile();

        if (!file_exists($localFilePath)) {
            throw new Exception("Arquivo local não encontrado: {$localFilePath}", 404);
        }

        $driveFile = new Google_Service_Drive_DriveFile();
        $driveFile->setName($remoteFileName);
        
        if (!empty($this->googleDriveAccount->folderId)) {
            $driveFile->setParents([$this->googleDriveAccount->folderId]);
        }

        $mimeType = mime_content_type($localFilePath) ?: 'application/octet-stream';
        
        try {
            $result = $this->service->files->create(
                $driveFile,
                [
                    'data' => file_get_contents($localFilePath),
                    'mimeType' => $mimeType,
                    'uploadType' => 'multipart'
                ]
            );

            $fileId = $result->getId();
            
            if ($this->settings->get('makePublic', false)) {
                $permission = new \Google_Service_Drive_Permission();
                $permission->setRole('reader');
                $permission->setType('anyone');
                $this->service->permissions->create($fileId, $permission);
                
                return "https://drive.google.com/file/d/{$fileId}/view";
            }

            return "https://drive.google.com/file/d/{$fileId}/view";
            
        } catch (\Exception $e) {
            throw new Exception("Erro ao fazer upload para Google Drive: " . $e->getMessage(), 500);
        }
    }

    protected function doDelete(DeleteCloudServiceFile $file)
    {
        $fileName = $file->getRemoteFileName();
        
        try {
            $files = $this->service->files->listFiles([
                'q' => "name='{$fileName}' and trashed=false",
                'fields' => 'files(id,name)'
            ]);

            $fileList = $files->getFiles();
            
            if (empty($fileList)) {
                throw new Exception("Arquivo '{$fileName}' não encontrado no Google Drive.", 404);
            }

            $fileToDelete = $fileList[0];
            $fileId = $fileToDelete->getId();
            
            $this->service->files->delete($fileId);
            
            return "O arquivo '{$fileName}' foi deletado com sucesso do Google Drive.";
            
        } catch (\Exception $e) {
            throw new Exception("Erro ao deletar arquivo do Google Drive: " . $e->getMessage(), 500);
        }
    }

    protected function afterExecute() { }
}
