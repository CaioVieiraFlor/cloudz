<?php

namespace Cloudz\AccountValidation;

interface AccountValidationStrategy
{
    public function validate(array $accountData);
}