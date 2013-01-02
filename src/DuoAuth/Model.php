<?php

namespace DuoAuth;

class Model
{
    /**
     * Properties of the current model
     * @var array
     */
    protected $properties = array();

    /**
     * Values for the current model
     * @var array
     */
    protected $values = array();

    /**
     * Default integration to use for the object
     * @var \DuoAuth\Integrations
     */
    protected $integration = null;

    public function __construct($data = null)
    {
        if ($data !== null) {
            $this->load($data);
        }
    }

    /**
     * Load the given data into the current object
     * 
     * @param array $data Data to load
     * @return boolean True on finish
     */
    public function load($data)
    {
        foreach (get_object_vars($data) as $index => $value) {
            // see what the type is
            if (array_key_exists($index, $this->properties)) {
                $config = $this->properties[$index];
                if ($config['type'] == 'array') {

                    // see if we have a type to try to cast
                    if (isset($config['map'])) {
                        $tmp = array();
                        $className = $config['map'];
                        foreach ($value as $mapData) {
                            $tmp[] = new $className($mapData);
                        }
                        $this->values[$index] = $tmp;

                    } else {
                        $this->values[$index] = $value;
                    }
                } else {
                    $this->values[$index] = $value;        
                }
            }
        }
        return true;
    }

    /**
     * Magic "get" method
     * 
     * @param string $property Property name
     * @return mixed|null Property value if it exists, null if not
     */
    public function __get($property)
    {
        return (array_key_exists($property, $this->values)) 
            ? $this->values[$property] : null;
    }

    /**
     * Magic "set" method
     * 
     * @param string $property Property name
     * @param mixed $value Property value
     */
    public function __set($property, $value)
    {
        $this->values[$property] = $value;
    }

    /**
     * Get a new Request instance
     * 
     * @param string $integration Name of integration to use
     * @return null|\DuoAuth\Request
     */
    public function getRequest($integration = null)
    {
        $integration = ($integration !== null) ? $integration : $this->integration;

        if ($integration !== null) {
            $className = '\\DuoAuth\\Integrations\\'.ucwords($integration);
            $int = new $className();
            return $int->getRequest();
        } else {
            return null;
        }
    }

    /**
     * Find records based on the given path and parameters
     * 
     * @param string $path Path to request
     * @param string $type Namespaced object type/classname to return
     * @param array $params Set of parameters to apply to request
     * @return array|boolean Either it populates the current object, returns the set or false
     */
    public function find($path, $type, $params = null)
    {
        $request = $this->getRequest()
            ->setPath($path);

        if ($params !== null && is_array($params)) {
            $request->setParams($params);
        }

        $response = $request->send();

        if ($response->success() == true) {
            $body = $response->getBody();

            if (is_array($body)) {
                if (count($body) == 1) {
                    $this->load($body[0]);
                } else {
                    $users = array();
                    foreach ($body as $index => $user) {
                        $u = new $type();
                        $u->load($user);
                        $users[$index] = $u;
                    }
                    return $users;
                }
            } else {
                // it's probably a single instance too
                $this->load($body);
            }
        } else {
            return false;
        }
    }
}