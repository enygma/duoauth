<?php

namespace DuoAuth;

class Model
{
    protected $properties = array();
    protected $values = array();
    protected $integration = null;

    public function __construct($data = null)
    {
        if ($data !== null) {
            $this->load($data);
        }
    }

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

    public function __get($property)
    {
        return (array_key_exists($property, $this->values)) 
            ? $this->values[$property] : null;
    }
    public function __set($property, $value)
    {
        $this->values[$property] = $value;
    }

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
}