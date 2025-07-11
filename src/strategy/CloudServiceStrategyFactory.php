<?php

namespace Cloudz\Strategy;

use InvalidArgumentException;
use Cloudz\CloudServiceSettings;
use Cloudz\CloudServiceTypes;
use Cloudz\Strategy\AWSS3Strategy;
use Cloudz\Strategy\FTPStrategy;
use Cloudz\Strategy\SFTPStrategy;
use Cloudz\Strategy\GoogleDriveStrategy;

class CloudServiceStrategyFactory 
{
    public static function assemble(string $cloudServiceType, $cloudServiceAccount, CloudServiceSettings $settings) 
    {
        switch ($cloudServiceType) {
            case CloudServiceTypes::FTP_ACCOUNT:
                if ($cloudServiceAccount->useSSH) {
                    return new SFTPStrategy($cloudServiceAccount, $settings);
                }
                return new FTPStrategy($cloudServiceAccount, $settings);
            case CloudServiceTypes::AWS_S3_ACCOUNT:
                return new AWSS3Strategy($cloudServiceAccount, $settings);
            case CloudServiceTypes::GOOGLE_DRIVE_ACCOUNT:
                return new GoogleDriveStrategy($cloudServiceAccount, $settings);
            default:
                throw new InvalidArgumentException('Tipo de serviço da nuvem inválido ou não implementado.');
        }
    }
}