<?php

namespace DuoAuth\Integrations;

class Auth2 extends \DuoAuth\Integration
{
    protected $alias = 'auth';

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

        // add the timestamp to the hash options
        $request->setHashOptions(
            array('date' => date('r'))
        );

        return $request;
    }
}