<?php

namespace CloudZ\Strategy;

use InvalidArgumentException;
use CloudZ\CloudServiceSettings;
use CloudZ\CloudServiceTypes;
use CloudZ\Strategy\AWSS3Strategy;
use CloudZ\Strategy\FTPStrategy;
use CloudZ\Strategy\SFTPStrategy;

class CloudServiceStrategyFactory 
{
    public static function assemble(string $cloudServiceType, $cloudServiceAccount, CloudServiceSettings $settings) 
    {
        switch ($cloudServiceType) {
            case CloudServiceTypes::FTP_ACCOUNT:
                if ($cloudServiceAccount->useSSH == 'S') {
                    return new SFTPStrategy($cloudServiceAccount, $settings);
                }
                return new FTPStrategy($cloudServiceAccount, $settings);
            case CloudServiceTypes::AWS_S3_ACCOUNT:
                return new AWSS3Strategy($cloudServiceAccount, $settings);
            default:
                throw new InvalidArgumentException('Tipo de serviço da nuvem inválido ou não implementado.');
        }
    }
}