<?php

namespace Cloudz\FTP;

use Cloudz\Strategy\CloudServiceStrategy;

abstract class StrategyBasedOnProtocolFTP extends CloudServiceStrategy
{
    protected abstract function login();
    protected abstract function changeToWorkDir();
}