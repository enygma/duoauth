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

    /**
     * Get the list of users for the current account
     * 
     * @return mixed|boolean User list or false if errored
     */
    public function getUsers()
    {
        $request = $this->getRequest()
            ->setPath('/admin/v1/users');

        $response = $this->execute($request);
        if ($response->success() == true) {
            return $response->getBody();
        } else {
            return false;
        }
    }

    /**
     * Get the information for a single user
     * 
     * @param string $username Username to search on
     * @return mixed/boolean Result if found, otherwise false
     */
    public function getUser($username)
    {
        $request = $this->getRequest()
            ->setParams(array('username' => $username))
            ->setPath('/admin/v1/users');

        $response = $this->execute($request);

        if ($response->success() == true) {
            return $response->getBody();
        } else {
            return false;
        }
    }
}