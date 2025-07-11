<?php

namespace Cloudz;

use Exception;
use Cloudz\AccountValidation\AccountValidationFactory;
use Cloudz\Strategy\CloudServiceStrategy;
use Cloudz\Strategy\CloudServiceStrategyFactory;

final class CloudService
{
    private string $type;
    private object $account;
    public CloudServiceSettings $settings;
    private CloudServiceStrategy $strategy;

    public function __construct(string $type, array $accountData)
    {
        $this->type = $type;
        $this->settings = new CloudServiceSettings();
        
        try {
            $accountValidationStrategy = AccountValidationFactory::assemble($this->type);
            $accountValidationStrategy->validate($accountData);
            
            $this->account = CloudServiceAccountFactory::assemble($this->type, $accountData);
            $this->strategy = CloudServiceStrategyFactory::assemble($this->type, $this->account, $this->settings);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function upload(CloudServiceFile $file)
    {
        return $this->strategy->upload($file); 
    }

    function delete(DeleteCloudServiceFile $file)
    {
        return $this->strategy->delete($file);
    }
}