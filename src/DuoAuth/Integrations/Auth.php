<?php

namespace DuoAuth\Integrations;

class Auth extends \DuoAuth\Integration
{
    /**
     * Update the request with some settings (host, init key, secret key)
     *
     * @param \DuoAuth\Request $request Request object
     * @return \DuoAuth\Request object
     */
    public function updateRequest($request)
    {
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