<?php

namespace CloudZ;

class DeleteCloudServiceFile
{
    private string $remoteAccessUrl;

    public function __construct(string $remoteAccessUrl)
    {
        $this->remoteAccessUrl = $remoteAccessUrl;
    }

    public function getRemoteFileName()
    {
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $this->remoteAccessUrl, $matches)) {
            return $matches[1];
        }
        
        $urlParts = parse_url($this->remoteAccessUrl);
        if (isset($urlParts['path'])) {
            $pathParts = explode('/', trim($urlParts['path'], '/'));
            $fileName = end($pathParts);
            
            if (!empty($fileName)) {
                return $fileName;
            }
        }
        
        $remoteAccessUrl = explode('/', $this->remoteAccessUrl);
        $remoteFileName = end($remoteAccessUrl);
        return $remoteFileName;
    }

    public function getFileId()
    {
        return $this->getRemoteFileName();
    }
}
