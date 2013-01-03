<?php

namespace DuoAuth;

class Log extends \DuoAuth\Model
{
    protected $properties = array(
        'action' => array(
            'type' => 'string'
        ),
        'description' => array(
            'type' => 'string'
        ),
        'object' => array(
            'type' => 'string'
        ),
        'timestamp' => array(
            'type' => 'string'
        ),
        'username' => array(
            'type' => 'string'
        )
    );

    protected $integration = 'admin';

    public function find()
    {
        $request = $this->getRequest()
            ->setPath('/admin/v1/logs/'.$this->type);

        $response = $request->send();

        $body = $response->getBody();
        $results = array();
        $className = '\\DuoAuth\\Logs\\'.ucwords(strtolower($this->type));

        foreach ($body as $index => $entry) {

            $results[$index] = new $className($entry);
        }
        return $results;
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
}