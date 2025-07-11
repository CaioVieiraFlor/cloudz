<?php

namespace CloudZ\GoogleDrive;

class GoogleDriveAccountBuilder
{
    private GoogleDriveAccount $account;

    public function __construct()
    {
        $this->account = new GoogleDriveAccount();
    }

    public function usingClientId(string $clientId): self
    {
        $this->account->clientId = $clientId;
        return $this;
    }

    public function usingClientSecret(string $clientSecret): self
    {
        $this->account->clientSecret = $clientSecret;
        return $this;
    }

    public function usingRefreshToken(string $refreshToken): self
    {
        $this->account->refreshToken = $refreshToken;
        return $this;
    }

    public function usingAccessToken(string $accessToken): self
    {
        $this->account->accessToken = $accessToken;
        return $this;
    }

    public function inFolder(?string $folderId): self
    {
        $this->account->folderId = $folderId;
        return $this;
    }

    public function withType(string $type): self
    {
        $this->account->type = $type;
        return $this;
    }

    public function build(): GoogleDriveAccount
    {
        return $this->account;
    }
}
