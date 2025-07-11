<?php

namespace CloudZ;

use InvalidArgumentException;
use CloudZ\CloudServiceTypes;
use CloudZ\FTP\FTPAccountBuilder;
use CloudZ\AWS\AWSS3\AWSS3AccountBuilder;
use CloudZ\GoogleDrive\GoogleDriveAccountBuilder;
use CloudZ\Utility\ArrayHelper;

class CloudServiceAccountFactory 
{
    public static function assemble(string $cloudServiceType, array $accountData) 
    {
        switch ($cloudServiceType) {
            case CloudServiceTypes::FTP_ACCOUNT:
                $FTPAccountBuilder = new FTPAccountBuilder();
                $FTPAccount = $FTPAccountBuilder
                    ->usingHost(ArrayHelper::get($accountData, 'host'))
                    ->atPort(ArrayHelper::get($accountData, 'port', 21))
                    ->withUser(ArrayHelper::get($accountData, 'user'))
                    ->withPassword(ArrayHelper::get($accountData, 'password'))
                    ->beingPassive(ArrayHelper::get($accountData, 'isPassive', true))
                    ->atWorkDir(ArrayHelper::get($accountData, 'dirWork', ''))
                    ->onAccessUrl(ArrayHelper::get($accountData, 'urlAcess', ''))
                    ->usingSSH(ArrayHelper::get($accountData, 'useSSH', false))
                    ->build();
                return $FTPAccount;

            case CloudServiceTypes::AWS_S3_ACCOUNT:
                $AWSS3AccountBuilder = new AWSS3AccountBuilder();
                $AWSS3Account = $AWSS3AccountBuilder
                    ->usingKey(ArrayHelper::get($accountData, 'key'))
                    ->usingSecretKey(ArrayHelper::get($accountData, 'secretKey'))
                    ->atRegion(ArrayHelper::get($accountData, 'region'))
                    ->withType(ArrayHelper::get($accountData, 'type'))
                    ->inBucket(ArrayHelper::get($accountData, 'bucketName'))
                    ->build();
                return $AWSS3Account;

            case CloudServiceTypes::GOOGLE_DRIVE_ACCOUNT:
                $googleDriveAccountBuilder = new GoogleDriveAccountBuilder();
                $googleDriveAccount = $googleDriveAccountBuilder
                    ->usingClientId(ArrayHelper::get($accountData, 'clientId'))
                    ->usingClientSecret(ArrayHelper::get($accountData, 'clientSecret'))
                    ->usingRefreshToken(ArrayHelper::get($accountData, 'refreshToken', ''))
                    ->usingAccessToken(ArrayHelper::get($accountData, 'accessToken', ''))
                    ->inFolder(ArrayHelper::get($accountData, 'folderId', null))
                    ->withType(ArrayHelper::get($accountData, 'type', 'GOOGLE-DRIVE'))
                    ->build();
                return $googleDriveAccount;

            default:
                throw new InvalidArgumentException('Tipo de serviço da nuvem inválido ou não implementado.');
        }
    }
}
