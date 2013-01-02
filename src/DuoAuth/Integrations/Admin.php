<?php

namespace DuoAuth\Integrations;

class Admin extends \DuoAuth\Integration
{
    public function getRequest()
    {
        $request = new \DuoAuth\Request();

        $request->setHostname($this->getHostname())
            ->setIntKey($this->getIntegration())
            ->setSecretKey($this->getSecret());

        // add the timestamp to the hash options
        $request->setHashOptions(
            array('date' => date('r'))
        );

        return $request;
    }
}