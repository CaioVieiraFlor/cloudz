<?php

namespace Cloudz\Response;

class CloudServiceResponseSuccess extends CloudServiceResponse 
{
    public function __construct(int $code, string $url)
    {
        parent::__construct($code);
        $this->url = $url;
    }
}