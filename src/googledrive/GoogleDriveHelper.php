<?php

namespace CloudZ\GoogleDrive;

use Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;

class GoogleDriveHelper
{
    private Google_Client $client;
    private Google_Service_Drive $service;
    private GoogleDriveAccount $account;

    public function __construct(GoogleDriveAccount $account)
    {
        $this->account = $account;
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $this->client = new Google_Client();
        $this->client->setClientId($this->account->clientId);
        $this->client->setClientSecret($this->account->clientSecret);
        $this->client->setAccessType('offline');
        $this->client->addScope(Google_Service_Drive::DRIVE_FILE);
        
        if (!empty($this->account->refreshToken)) {
            $this->client->refreshToken($this->account->refreshToken);
        } elseif (!empty($this->account->accessToken)) {
            $this->client->setAccessToken($this->account->accessToken);
        }
        
        $this->service = new Google_Service_Drive($this->client);
    }

    public function createFolder(string $folderName, ?string $parentFolderId = null)
    {
        if (empty($folderName)) {
            throw new Exception('Nome da pasta é obrigatório.');
        }

        $folder = new Google_Service_Drive_DriveFile();
        $folder->setName($folderName);
        $folder->setMimeType('application/vnd.google-apps.folder');
        
        if ($parentFolderId) {
            $folder->setParents([$parentFolderId]);
        }

        try {
            $result = $this->service->files->create($folder);
            return $result->getId();
        } catch (\Exception $e) {
            throw new Exception("Erro ao criar pasta no Google Drive: " . $e->getMessage());
        }
    }

    public function listFiles(?string $folderId = null, int $maxResults = 10)
    {
        try {
            $query = "trashed=false";
            if ($folderId) {
                $query .= " and '{$folderId}' in parents";
            }

            $files = $this->service->files->listFiles([
                'q' => $query,
                'pageSize' => $maxResults,
                'fields' => 'files(id,name,mimeType,size,createdTime)'
            ]);

            return $files->getFiles();
        } catch (\Exception $e) {
            throw new Exception("Erro ao listar arquivos do Google Drive: " . $e->getMessage());
        }
    }

    public function findFolderByName(string $folderName, ?string $parentFolderId = null)
    {
        try {
            $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and trashed=false";
            if ($parentFolderId) {
                $query .= " and '{$parentFolderId}' in parents";
            }

            $files = $this->service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name)'
            ]);

            $fileList = $files->getFiles();
            return !empty($fileList) ? $fileList[0]->getId() : null;
        } catch (\Exception $e) {
            throw new Exception("Erro ao buscar pasta no Google Drive: " . $e->getMessage());
        }
    }

    public function makeFilePublic(string $fileId)
    {
        try {
            $permission = new Google_Service_Drive_Permission();
            $permission->setRole('reader');
            $permission->setType('anyone');
            
            $this->service->permissions->create($fileId, $permission);
            return true;
        } catch (\Exception $e) {
            throw new Exception("Erro ao tornar arquivo público: " . $e->getMessage());
        }
    }

    public function getFileInfo(string $fileId)
    {
        try {
            $file = $this->service->files->get($fileId, [
                'fields' => 'id,name,mimeType,size,createdTime,modifiedTime,webViewLink,webContentLink'
            ]);

            return [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize(),
                'createdTime' => $file->getCreatedTime(),
                'modifiedTime' => $file->getModifiedTime(),
                'webViewLink' => $file->getWebViewLink(),
                'webContentLink' => $file->getWebContentLink()
            ];
        } catch (\Exception $e) {
            throw new Exception("Erro ao obter informações do arquivo: " . $e->getMessage());
        }
    }

    public function testConnection()
    {
        try {
            $this->service->files->listFiles(['pageSize' => 1]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
