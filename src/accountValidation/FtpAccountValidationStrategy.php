<?php

namespace CloudZ\AccountValidation;

class FtpAccountValidationStrategy implements AccountValidationStrategy
{
    public function validate(array $accountData)
    {
        $requiredFields = ['host', 'port', 'user', 'password', 'isPassive', 'useSSH'];
        foreach ($requiredFields as $field) {
            if (empty($accountData[$field])) {
                throw new \InvalidArgumentException("FTP: O campo {$field} é obrigatório.");
            }
        }
    }
}
