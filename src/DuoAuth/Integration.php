<?php

namespace DuoAuth;

class Integration
{
    /**
     * Secret key (provided by Duo)
     * @var string
     */
    protected $secretKey = null;

    /**
     * Integration key (provided by Duo)
     * @var string
     */
    protected $intKey = null;

    /**
     * API hostname (provided by Duo)
     * @var string
     */
    protected $apiHostname = null;

    public function __construct($config = null)
    {
        // configure the object
        if ($config == null) {
            $this->loadConfig();
        }
    }

    public function loadConfig()
    {
        $file = getcwd().'/duoauth.json';
        if (is_file($file)) {
            $cfg = json_decode(file_get_contents($file));
            
            if ($cfg !== false) {
                $type = strtolower(str_replace(__NAMESPACE__.'\\Integrations\\', '', get_class($this)));
                if (isset($cfg->integrations) && isset($cfg->integrations->$type)) {
                    foreach(get_object_vars($cfg->integrations->$type) as $index => $value) {
                        $method = 'set'.ucwords(strtolower($index));
                        if (method_exists($this, $method)) {
                            $this->$method($value);
                        }
                    }
                }
            }
        }
    }

    public function getRequest()
    {
        $request = new \DuoAuth\Request();

        $request->setHostname($this->getHostname())
            ->setIntKey($this->getIntegration())
            ->setSecretKey($this->getSecret());
        
        return $request;
    }

    /**
     * Set the object's secret key
     *
     * @param string $key Secret key
     * @return \DuoAuth\Auth instance
     */
    public function setSecret($key)
    {
        $this->secretKey = $key;
        return $this;
    }

    /**
     * Get the object's secret key value
     *
     * @return string Secret key
     */
    public function getSecret()
    {
        return $this->secretKey;
    }

    /**
     * Set the object's integration key
     *
     * @param string $key Integration key value
     * @return \DuoAuth\Auth instance
     */
    public function setIntegration($key)
    {
        $this->intKey = $key;
        return $this;
    }

    /**
     * Get the current integration key value
     *
     * @return string Integration key
     */
    public function getIntegration()
    {
        return $this->intKey;
    }

    /**
     * Set up the object's Hostname value
     *
     * @param string $hostname Hostname
     * @return \DuoAuth\Auth instance
     */
    public function setHostname($hostname)
    {
        $this->apiHostname = $hostname;
        return $this;
    }

    /**
     * Get the current Hostname value
     *
     * @return string Hostname value
     */
    public function getHostname()
    {
        return $this->apiHostname;
    }
}

?>