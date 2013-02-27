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

    /**
     * Current HTTP client object
     * @var object
     */
    protected $client = null;

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
                $type = ($this->getAlias() !== null) ? $this->getAlias() : $type;

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
     * Get the client for the Integration
     *
     * @return object Client object
     */
    public function getClient()
    {
        $client = new \Guzzle\Http\Client();
        $this->setClient($client);
        return $client;
    }

    /**
     * Set the client for the Integration
     *
     * @param object $client Client object
     */
    public function setClient($client)
    {
        $this->client = $client;
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
     * Get the alias of the integration
     *
     * @return string Alias name
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Get the full list of integrations
     *
     * @return array List of integration data (set of \DuoAuth\Integration)
     */
    public function findAll()
    {
        $this->integration = 'admin';
        return $this->find('/admin/v1/integrations', '\\DuoAuth\\Integration');
    }

    /**
     * Find an integration by its Integration ID
     *
     * @param string $integrationId Integration ID to find
     * @return \DuoAuth\Integration object
     */
    public function findById($integrationId)
    {
        $this->integration = 'admin';
        return $this->find('/admin/v1/integrations/'.$integrationId, '\\DuoAuth\\Integration');
    }

    /**
     * Create/update the given Integration
     *
     * @param boolean $reset Send a key reset (integration key)
     * @return boolean Success/fail of request
     */
    public function save($reset = false)
    {
        $path = ($this->integration_key == null)
            ? '/admin/v1/integrations' : '/admin/v1/integrations/'.$this->integration_key;

        $params = array(
            'name' => $this->name,
            'enroll_policy' => $this->enroll_policy,
            'greeting' => $this->greeting,
            'notes' => $this->notes,
            'visual_style' => $this->visual_style,
            'adminapi_admins' => $this->adminapi_admins,
            'adminapi_info' => $this->adminapi_info,
            'adminapi_integrations' => $this->adminapi_integrations,
            'adminapi_read_log' => $this->adminapi_read_log,
            'adminapi_read_resource' => $this->adminapi_read_resource,
            'adminapi_settings' => $this->adminapi_settings,
            'adminapi_write_resource' => $this->adminapi_write_resource
        );

        if ($reset == true) {
            $params['reset_secret_key'] = 1;
        }

        $request = $this->getRequest()
            ->setMethod('POST')
            ->setParams($params)
            ->setPath($path);

        $response = $request->send();

        if ($response !== null && $response->success() == true) {
            $body = $response->getBody();
            return $this->load($body);
        } else {
            return false;
        }
    }
}

?>