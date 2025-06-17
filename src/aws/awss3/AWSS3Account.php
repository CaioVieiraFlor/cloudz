<?php

namespace CloudZ\AWS\AWSS3;

use CloudZ\AWS\AWSAccount;

class AWSS3Account extends AWSAccount
{
    private int $code;
    public string $bucketName;

    public function __construct(int $code)
    {
        $this->code = $code;
    }
}