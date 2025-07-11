<?php

namespace CloudZ\AccountValidation;

interface AccountValidationStrategy
{
    public function validate(array $accountData);
}
