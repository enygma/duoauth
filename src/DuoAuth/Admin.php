<?php

namespace DuoAuth;

class Admin extends \DuoAuth\Connection
{
    /**
     * Make a new request object and assign some default values
     *
     * @return \DuoAuth\Request instance
     */
    public function getRequest()
    {
        $request = new \DuoAuth\Request();

        $request->setHostname($this->getApiHostname())
            ->setIntKey($this->getIntKey())
            ->setSecretKey($this->getSecretKey());

        // add the timestamp to the hash options
        $request->setHashOptions(
            array('date' => date('r'))
        );

        return $request;
    }

    public function getUsers()
    {
        $request = $this->getRequest()
            ->setPath('/admin/v1/users');

        $response = $this->execute($request);
        if (isset($response['response']) && !empty($response['response'])) {
            return $response['response'];
        } else {
            return false;
        }
    }
}