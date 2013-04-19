<?php

namespace DuoAuth;

class Request
{
    /**
     * Request path
     * @var string
     */
    private $path = null;

    /**
     * Curent client object
     * @var \Guzzle\Http\Client
     */
    private $client = null;

    /**
     * HTTP method for the request
     * @var string
     */
    private $method = 'GET';

    /**
     * Parameters for the request
     * @var array
     */
    private $params = array();

    /**
     * API hostname for request
     * @var string
     */
    private $hostname = null;

    /**
     * Integration key (used in auth)
     * @var string
     */
    private $intKey = null;

    /**
     * Secret key (used in hash generation)
     * @var string
     */
    private $secretKey = null;

    /**
     * List of errors on current request
     * @var array
     */
    private $errors = array();

    /**
     * Additional options to use in building the hash
     * @var array
     */
    private $hashOptions = array();

    /**
     * Initialize the Request object
     */
    public function __construct($client = null)
    {
        $this->setClient($client);
    }

    /**
     * Undefined methods should be passed directly to the Guzzle client (if exist)
     *
     * @param string $func Function name
     * @param array $args Function arguments
     * @return mixed|boolean Function return or false on not-exist
     */
    public function __call($func, $args)
    {
        if (method_exists($this->client, $func)) {
            return call_user_func_array(array($this->client, $func), $args);
        }
        return false;
    }

    /**
     * Build canonical parameter string based off values in the current object
     *
     * @return string parameter string to be signed
     */
    protected function getCanonParams()
    {
        $params = $this->getParams();
        ksort($params);
        return str_replace(
            array('+', '%7E'),
            array('%20', '~'),
            http_build_query($params)
        );
    }

    /**
     * Build canonical request to sign based off values in the current object
     *
     * @return string request string to be signed
     */
    protected function getCanonRequest()
    {
        $hash = array();
        $addlHashOptions = $this->getHashOptions();
        if (!empty($addlHashOptions)) {
            $hash = array_merge($hash, $addlHashOptions);
        }

        $hash[] = strtoupper($this->getMethod());
        $hash[] = $this->getHostname();
        $hash[] = $this->getPath();
        $hash[] = $this->getCanonParams();

        return implode("\n", $hash);
    }

    /**
     * Build the hash header based off values in the current object
     *
     * @return string SHA1 hash for request contents
     */
    public function buildHashHeader()
    {
        return hash_hmac(
            'sha1',
            $this->getCanonRequest(),
            $this->getSecretKey()
        );
    }

    /**
     * Send the request to the API
     *
     * @return string|boolean Parsed json if successful, false if not
     */
    public function send()
    {
        $path = 'https://'.$this->getHostname().$this->getPath();
        $method = strtolower($this->getMethod());
        $client = $this->getClient();

        $hash = $this->buildHashHeader();
        $params = $this->getParams();
        ksort($params);

        if ($method == 'get') {
            $path .= '?'.http_build_query($params);
        }

        // Guzzle doesn't add the date header, so we put it in manually
        $request = $client->$method($path, array('Date' => date('r')), $params)
            ->setAuth($this->getIntKey(), $hash);

        $response = new \DuoAuth\Response();
        try {
            $response->setData($request->send());
            return $response;

        } catch (\Exception $e) {
            \DuoAuth\Error::add($e->getMessage());
            $this->setError($e->getMessage());
            return $response;
        }

    }

    /**
     * Get additional options for the hash construction
     *
     * @return array Additional options
     */
    public function getHashOptions()
    {
        return $this->hashOptions;
    }

    /**
     * Set options to include in the hash
     *
     * @param array $options Additional hash options
     * @return \DuoAuth\Request instance
     */
    public function setHashOptions($options)
    {
        $this->hashOptions = $options;
        return $this;
    }

    /**
     * Get the current error list
     *
     * @return array Error list
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set an error message in the array
     * 
     * @param string $msg Error message
     * @return \DuoAuth\Request instance
     */
    public function setError($msg)
    {
        $this->errors[] = $msg;
        return $this;
    }

    /**
     * Set the object's integration key
     *
     * @param string $key Integration key
     * @return \DuoAuth\Request instance
     */
    public function setIntKey($key)
    {
        $this->intKey = $key;
        return $this;
    }

    /**
     * Get the object's integration key
     *
     * @return string Integration key
     */
    public function getIntKey()
    {
        return $this->intKey;
    }

    /**
     * Set the secret key for request
     *
     * @param string $key Secret key
     * @return \DuoAuth\Request instance
     */
    public function setSecretKey($key)
    {
        $this->secretKey = $key;
        return $this;
    }
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Set the hostname for the current request
     *
     * @param string $hostname Hostname to set
     * @return \DuoAuth\Request instance
     */
    public function setHostname($hostname)
    {
        $this->hostname = strtolower($hostname);
        return $this;
    }

    /**
     * Get the hostname for the current request
     * @return string Current hostname
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set the HTTP method to use for the request
     *     NOTE: Class default is GET
     *
     * @param string $method HTTP method
     * @return \DuoAuth\Request instance
     */
    public function setMethod($method)
    {
        $this->method = strtolower($method);
        return $this;
    }

    /**
     * Get the HTTP method for request
     *
     * @return string HTTP method
     */
    public function getMethod()
    {
        return strtoupper($this->method);
    }

    /**
     * Set the client for the current request
     *
     * @param $client Guzzle client
     * @return \DuoAuth\Request instance
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get the current client object
     *
     * @return \Guzzle\Http\Client instance
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * [setPath description]
     *
     * @param string $path Path to set
     * @param string $format Format for reqponse (default: json). Only supported when calling the Verify or REST APIs.
     * @return \DuoAuth\Request instance
     */
    public function setPath($path, $format = null)
    {
        if ($format !== null) {
            $path .= '.'.$format;
        }
        $this->path = $path;
        return $this;
    }

    /**
     * Get the request's current path
     *
     * @return string Current path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set parameters on the request
     *
     * @param array $params Set of parameters
     * @return \DuoAuth\Request instance
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set a single parameter on the request
     *
     * @param string $name Name of parameter
     * @param string $value Value of parameter
     * @return \DuoAuth\Request instance
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Get the current request's params
     *
     * @return array Parameter set
     */
    public function getParams()
    {
        return $this->params;
    }

}
