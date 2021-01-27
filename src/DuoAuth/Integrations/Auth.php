<?php

namespace DuoAuth\Integrations;

class Auth extends \DuoAuth\Integration
{
    /**
     * Get a new request & update with some settings (host, init key, secret key)
     * 
     * @return \DuoAuth\Request object
     */
    public function getRequest($integration = null)
    {
        $request = new \DuoAuth\Request($this->getClient());
        
        $request->setHostname($this->getHostname())
            ->setIntKey($this->getIntegration())
            ->setSecretKey($this->getSecret());

        return $request;
    }
}
