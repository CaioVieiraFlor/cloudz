<?php

namespace CloudZ\AccountValidation;

use CloudZ\CloudServiceTypes;

class AccountValidationFactory
{
    public static function assemble(string $cloudServiceType)
    {
        switch ($cloudServiceType) {
            case CloudServiceTypes::FTP_ACCOUNT:
                return new FtpAccountValidationStrategy();
            case CloudServiceTypes::AWS_S3_ACCOUNT:
                return new AWSS3AccountValidationStrategy();
            case CloudServiceTypes::GOOGLE_DRIVE_ACCOUNT:
                return new GoogleDriveAccountValidationStrategy();
            default:
                throw new \InvalidArgumentException('Tipo de serviço da nuvem inválido ou não implementado.');
        }
    }
}
