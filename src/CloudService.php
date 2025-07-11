<?php

namespace CloudZ;

use Exception;
use CloudZ\AccountValidation\AccountValidationFactory;
use CloudZ\CloudServiceAccountFactory;
use CloudZ\Strategy\CloudServiceStrategy;
use CloudZ\Strategy\CloudServiceStrategyFactory;

final class CloudService
{
    private string $type;
    public object $account;
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
