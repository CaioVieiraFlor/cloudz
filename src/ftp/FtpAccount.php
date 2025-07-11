<?php 

namespace CloudZ\FTP;

class FTPAccount
{
    public string $host = '';
    public int $port;
    public string $user = '';
    public string $password = '';
    public bool $isPassive = false;
    public string $workDir = '';
    public string $dirPlugin = '';
    public string $accessUrl = '';
    public bool $useSSH = false;
}
