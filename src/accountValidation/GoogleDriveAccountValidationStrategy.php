<?php

namespace CloudZ\AccountValidation;

class GoogleDriveAccountValidationStrategy implements AccountValidationStrategy
{
    public function validate(array $accountData)
    {
        $requiredFields = ['clientId', 'clientSecret'];
        foreach ($requiredFields as $field) {
            if (empty($accountData[$field])) {
                throw new \InvalidArgumentException("Google Drive: O campo {$field} é obrigatório.");
            }
        }

        if (empty($accountData['accessToken']) && empty($accountData['refreshToken'])) {
            throw new \InvalidArgumentException("Google Drive: É obrigatório fornecer pelo menos um accessToken ou refreshToken.");
        }
    }
}
