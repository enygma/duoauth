<?php

namespace DuoAuth\Devices\Tokens;

class Yubikey extends \DuoAuth\Devices\Token
{
    protected $properties = array(
        'token_id' => array(
            'type' => 'string'
        ),
        'type' => array(
            'type' => 'string',
            'required' => true
        ),
        'serial' => array(
            'type' => 'string',
            'required' => true
        ),
        'private_id' => array(
            'type' => 'string'
        ),
        'aes_key' => array(
            'type' => 'string',
            'required' => true
        )
    );

    protected $integration = 'admin';

    /**
     * Create and update the Token object
     *
     * @return boolean Success/fail of phone update
     */
    public function save()
    {
        // this is a Yubikey so the type is...
        $this->type = 'yk';
        $this->validate();

        $params = array(
            'type' => $this->type,
            'serial' => $this->serial,
            'private_id' => $this->private_id,
            'aes_key' => $this->aes_key
        );

        return $this->saveToken($params);
    }
}