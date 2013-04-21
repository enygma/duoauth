<?php

namespace DuoAuth\Devices\Tokens;

class D100 extends \DuoAuth\Devices\Token
{
    protected $properties = array(
        'token_id' => array(
            'type' => 'string'
        ),
        'type' => array(
            'type' => 'string'
        ),
        'serial' => array(
            'type' => 'string',
            'required' => true
        ),
        'secret' => array(
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
        $this->type = 'd1';
        $this->validate();

        $params = array(
            'type' => $this->type,
            'serial' => $this->serial,
            'secret' => $this->secret
        );

        return $this->saveToken($params);
    }

}

?>