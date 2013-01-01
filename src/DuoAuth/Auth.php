<?php

namespace DuoAuth;

class Auth extends \DuoAuth\Connection
{
    /**
     * Ping the Duo API
     *
     * @return boolean Pass/fail on the ping
     */
    public function ping()
    {
        // ping the API
        $request = $this->getRequest()->setPath('/rest/v1/ping');
        $response = $this->execute($request);

        return ($response->getBody() == 'pong') ? true : false;
    }

    /**
     * Send a "preauth" request
     * 
     * @param string $username Username to check
     * @return array Response data
     */
    public function preauth($username)
    {
        $request = $this->getRequest()
            ->setPath('/rest/v1/preauth')
            ->setMethod('POST')
            ->setParams(
                array('user' => $username)
            );

        $response = $this->execute($request);
        return $response;
    }

    /**
     * Validate a code given by the user
     *     NOTE: On failure, errors are appended to error list
     *
     * @param string $username Duo username for validation
     * @param string $code Inputted verification code
     * @param string $device Name for user device to use (default: 'phone1')
     * @return boolean Success/fail of validation
     */
    public function validateCode($username, $code, $device = 'phone1')
    {
        $request = $this->getRequest()
            ->setPath('/rest/v1/auth')
            ->setMethod('POST')
            ->setParams(
                array(
                    'user'   => $username,
                    'factor' => 'passcode',
                    'code'   => $code,
                    'phone'  => $device
                )
            );
        $response = $this->execute($request);
        $body = $response->getBody();

        if ($response->success() == true && $body->result !== 'deny') {
            return true;
        } else {
            $this->setErrors($body->status);
            return false;
        }
    }
}
