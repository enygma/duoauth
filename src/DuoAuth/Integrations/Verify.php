<?php

namespace DuoAuth\Integrations;

class Verify extends \DuoAuth\Integration
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

        return $request;
    }
}