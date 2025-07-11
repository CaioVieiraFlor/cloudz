<?php

namespace CloudZ\GoogleDrive;

class GoogleDriveAccount 
{
    public string $clientId;
    public string $clientSecret;
    public string $refreshToken;
    public string $accessToken;
    public ?string $folderId = null;
    public string $type;
}
