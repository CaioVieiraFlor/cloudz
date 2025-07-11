<?php

namespace CloudZ\AccountValidation;

class AWSS3AccountValidationStrategy implements AccountValidationStrategy
{
    public function validate(array $accountData)
    {
        $requiredFields = ['key', 'secretKey', 'region', 'bucketName'];
        foreach ($requiredFields as $field) {
            if (empty($accountData[$field])) {
                throw new \InvalidArgumentException("AWS S3: O campo {$field} é obrigatório.");
            }
        }
    }
}
