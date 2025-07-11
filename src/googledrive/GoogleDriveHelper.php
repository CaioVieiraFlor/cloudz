<?php

namespace Cloudz\GoogleDrive;

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

    /**
     * Cria uma nova pasta no Google Drive
     * @param string $folderName Nome da pasta a ser criada
     * @param string|null $parentFolderId ID da pasta pai (null para raiz)
     * @return string ID da pasta criada
     * @throws Exception
     */
    public function createFolder(string $folderName, ?string $parentFolderId = null): string
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

    /**
     * Lista arquivos e pastas de um diretório
     * @param string|null $folderId ID da pasta (null para raiz)
     * @param int $maxResults Número máximo de resultados
     * @return array Lista de arquivos/pastas
     * @throws Exception
     */
    public function listFiles(?string $folderId = null, int $maxResults = 10): array
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

    /**
     * Busca uma pasta pelo nome
     * @param string $folderName Nome da pasta
     * @param string|null $parentFolderId ID da pasta pai para buscar dentro
     * @return string|null ID da pasta encontrada ou null
     * @throws Exception
     */
    public function findFolderByName(string $folderName, ?string $parentFolderId = null): ?string
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

    /**
     * Torna um arquivo público
     * @param string $fileId ID do arquivo
     * @return bool Sucesso da operação
     * @throws Exception
     */
    public function makeFilePublic(string $fileId): bool
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

    /**
     * Obtém informações de um arquivo
     * @param string $fileId ID do arquivo
     * @return array Informações do arquivo
     * @throws Exception
     */
    public function getFileInfo(string $fileId): array
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

    /**
     * Verifica se a conexão com Google Drive está funcionando
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            $this->service->files->listFiles(['pageSize' => 1]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
