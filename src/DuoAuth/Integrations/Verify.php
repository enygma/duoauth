<?php

namespace DuoAuth\Integrations;

class Verify extends \DuoAuth\Integration
{
    /**
     * Get a new request & update with some settings (host, init key, secret key)
     * 
     * @return \DuoAuth\Request object
     */
    public function getRequest()
    {
        $request = new \DuoAuth\Request();

        $request->setHostname($this->getHostname())
            ->setIntKey($this->getIntegration())
            ->setSecretKey($this->getSecret());

        return $request;
    }
}