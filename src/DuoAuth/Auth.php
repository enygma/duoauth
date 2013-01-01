<?php

namespace DuoAuth;

class Auth
{
    /**
     * Secret key (provided by Duo)
     * @var string
     */
    private $secretKey = null;

    /**
     * Integration key (provided by Duo)
     * @var string
     */
    private $intKey = null;

    /**
     * API hostname (provided by Duo)
     * @var string
     */
    private $apiHostname = null;

    /**
     * Listing of errors from requests
     * @var array
     */
    private $errors = array();

    /**
     * Current Request object
     * @var \DuoAuth\Request
     */
    private $currentRequest = null;

    /**
     * Current Response
     * @var array
     */
    private $currentResponse = null;

    /**
     * Initialist the Auth object
     *
     * @param string $hostname API hostname
     * @param string $secret API secret key
     * @param string $int API integration key
     */
    public function __construct($hostname, $secret, $int)
    {
        $this->setApiHostname($hostname);
        $this->setSecretKey($secret);
        $this->setIntKey($int);
    }

    /**
     * Get the current Response object
     * 
     * @return \DuoAuth\Response instance
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * Set current Request object
     * 
     * @param \DuoAuth\Request $request Request object
     * @return \DuoAuth\Auth instance
     */
    public function setCurrentRequest(\DuoAuth\Request $request)
    {
        $this->currentRequest = $request;
        return $this;
    }

    /**
     * Get the current response information
     * 
     * @return array Response information
     */
    public function getCurrentResponse()
    {
        return $this->currentResponse;
    }

    /**
     * Set the current response data
     * 
     * @param array $response Response data
     * @return \DuoAuth\Auth instance
     */
    public function setCurrentResponse($response)
    {
        $this->currentResponse = $response;
        return $this;
    }

    /**
     * Set the object's secret key
     *
     * @param string $key Secret key
     * @return \DuoAuth\Auth instance
     */
    public function setSecretKey($key)
    {
        $this->secretKey = $key;
        return $this;
    }

    /**
     * Get the object's secret key value
     *
     * @return string Secret key
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Set the object's integration key
     *
     * @param string $key Integration key value
     * @return \DuoAuth\Auth instance
     */
    public function setIntKey($key)
    {
        $this->intKey = $key;
        return $this;
    }

    /**
     * Get the current integration key value
     *
     * @return string Integration key
     */
    public function getIntKey()
    {
        return $this->intKey;
    }

    /**
     * Set up the object's Hostname value
     *
     * @param string $hostname Hostname
     * @return \DuoAuth\Auth instance
     */
    public function setApiHostname($hostname)
    {
        $this->apiHostname = $hostname;
        return $this;
    }

    /**
     * Get the current Hostname value
     *
     * @return string Hostname value
     */
    public function getApiHostname()
    {
        return $this->apiHostname;
    }

    /**
     * Append the given error to the stack
     *
     * @param string $error Error message
     * @return \DuoAuth\Auth instance
     */
    public function setErrors($error)
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * Get the current list of errors
     *
     * @return array Error list
     */
    public function getErrors()
    {
        return $this->errors;
    }

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

        return $request;
    }

    /**
     * Ping the Duo API
     *
     * @return boolean Pass/fail on the ping
     */
    public function ping()
    {
        // ping the API
        $request = $this->getRequest()->setPath('/rest/v1/ping');
        $this->setCurrentRequest($request);
        $response = $request->send();
        $this->setCurrentResponse($response);
        return (isset($response['response']) && $response['response'] == 'pong') ? true : false;
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
        $this->setCurrentRequest($request);
        $response = $request->send();
        $this->setCurrentResponse($response);

        if (isset($response['response']['result']) && $response['response']['result'] !== 'deny') {
            return true;
        } else {
            $this->setErrors($response['response']['status']);
            return false;
        }
    }
}
