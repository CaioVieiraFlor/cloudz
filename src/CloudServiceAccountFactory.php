<?php

namespace CloudZ;

use InvalidArgumentException;
use CloudZ\CloudServiceTypes;
use CloudZ\FTP\FTPAccountBuilder;
use CloudZ\Tool\CloudServiceAccountTool;
use CloudZ\AWS\AWSS3\AWSS3AccountBuilder;
use CloudZ\Tool\JsonTools\CloudServiceJsonRealPaths;
use CloudZ\Tool\JsonTools\CloudServiceJsonTool;

class CloudServiceAccountFactory 
{
    public static function assemble(string $cloudServiceType, int $cloudServiceCode) 
    {
        switch ($cloudServiceType) {
            case CloudServiceTypes::FTP_ACCOUNT:
                $jsonOfAccounts = CloudServiceJsonTool::getJson(CloudServiceJsonRealPaths::getFTPRealPath());
                $FTPAccountData = CloudServiceAccountTool::selector($jsonOfAccounts->FTPAccount, $cloudServiceCode);
                
                $FTPAccountBuilder = new FTPAccountBuilder($FTPAccountData->code);
                $FTPAccount = $FTPAccountBuilder->usingHost($FTPAccountData->host)
                                                ->atPort($FTPAccountData->port)
                                                ->withUser($FTPAccountData->user)
                                                ->withPassword($FTPAccountData->password)
                                                ->beingPassive($FTPAccountData->isPassive)
                                                ->atWorkDir($FTPAccountData->dirWork)
                                                ->onAccessUrl($FTPAccountData->urlAcess)
                                                ->usingSSH($FTPAccountData->useSSH)
                                                ->build();
                return $FTPAccount;

            case CloudServiceTypes::AWS_S3_ACCOUNT:
                $jsonOfAccounts = CloudServiceJsonTool::getJson(CloudServiceJsonRealPaths::getAWSS3RealPath());
                $AWSS3AccountData = CloudServiceAccountTool::selector($jsonOfAccounts->AWSS3Account, $cloudServiceCode);
                
                $AWSS3AccountBuilder = new AWSS3AccountBuilder($AWSS3AccountData->code);
                $AWSS3Account = $AWSS3AccountBuilder->usingKey($AWSS3AccountData->AWSKey)
                                                    ->usingSecretKey($AWSS3AccountData->AWSSecretKey)
                                                    ->atRegion($AWSS3AccountData->AWSRegion)
                                                    ->withType($AWSS3AccountData->AWSType)
                                                    ->inBucket($AWSS3AccountData->bucketName)
                                                    ->build();
                return $AWSS3Account;

            default:
                throw new InvalidArgumentException('Tipo de serviço da nuvem inválido ou não implementado.');
        }
    }
}