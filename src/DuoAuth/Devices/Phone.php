<?php

namespace DuoAuth\Devices;

class Phone extends \DuoAuth\Model
{
    protected $properties = array(
        'activated' => array(
            'type' => 'string'
        ),
        'extension' => array(
            'type' => 'string'
        ),
        'name' => array(
            'type' => 'string'
        ),
        'number' => array(
            'type' => 'string'
        ),
        'phone_id' => array(
            'type' => 'string'
        ),
        'platform' => array(
            'type' => 'string'
        ),
        'postdelay' => array(
            'type' => 'string'
        ),
        'predelay' => array(
            'type' => 'string'
        ),
        'sms_passcodes_sent' => array(
            'type' => 'string'
        ),
        'type' => array(
            'type' => 'string'
        )
    );

    protected $integration = 'admin';

    public function associateWithUser($userId, $phoneId = null)
    {
        $phoneId = ($phoneId !== null) ? $phoneId : $this->phone_id;
        $request = $this->getRequest()
            ->setMethod('POST')
            ->setParams(array('phone_id' => $phoneId))
            ->setPath('/admin/v1/users/'.$userId.'/phones');

        $response = $request->send();
        if ($response->success() == true) {
            return $response->getBody();
        } else {
            return false;
        }
    }
}