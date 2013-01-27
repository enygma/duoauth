<?php

namespace DuoAuth\Integrations;

class Auth extends \DuoAuth\Integration
{
    public function getRequest()
    {
        $request = new \DuoAuth\Request();

        $request->setHostname($this->getHostname())
            ->setIntKey($this->getIntegration())
            ->setSecretKey($this->getSecret());

        return $request;
    }   
}