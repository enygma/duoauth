<?php

namespace DuoAuth;

class Integration extends \DuoAuth\Model
{
    protected $properties = array(
        'adminapi_admins' => array(
            'type' => 'string'
        ),
        'adminapi_info' => array(
            'type' => 'string'
        ),
        'adminapi_integrations' => array(
            'type' => 'string'
        ),
        'adminapi_read_log' => array(
            'type' => 'string'
        ),
        'adminapi_read_resource' => array(
            'type' => 'string'
        ),
        'adminapi_settings' => array(
            'type' => 'string'
        ),
        'adminapi_write_resource' => array(
            'type' => 'string'
        ),
        'enroll_policy' => array(
            'type' => 'string'
        ),
        'greeting' => array(
            'type' => 'string'
        ),
        'integration_key' => array(
            'type' => 'string'
        ),
        'name' => array(
            'type' => 'string'
        ),
        'notes' => array(
            'type' => 'string'
        ),
        'secret_key' => array(
            'type' => 'string'
        ),
        'type' => array(
            'type' => 'string'
        ),
        'visual_style' => array(
            'type' => 'string'
        )
    );

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

    /**
     * Get the full list of integrations
     * 
     * @return array List of integrations data
     */
    public function findAll()
    {
        $this->integration = 'admin';
        return $this->find('/admin/v1/integrations', '\\DuoAuth\\Integration');
    }

    public function findById($integrationId)
    {
        $this->integration = 'admin';
        return $this->find('/admin/v1/integrations/'.$integrationId, '\\DuoAuth\\Integration');
    }
}

?>