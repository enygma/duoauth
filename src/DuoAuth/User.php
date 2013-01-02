<?php

namespace DuoAuth;

class User extends \DuoAuth\Model
{
    protected $properties = array(
        'user_id' => array(
            'type' => 'string'
        ),
        'username' => array(
            'type' => 'string'
        ),
        'realname' => array(
            'type' => 'string'
        ),
        'status' => array(
            'type' => 'string'
        ),
        'groups' => array(
            'type' => 'array'
        ),
        'phones' => array(
            'type' => 'array',
            'map' => '\DuoAuth\Devices\Phone'
        ),
        'tokens' => array(
            'type' => 'array'
        )
    );

    protected $integration = 'admin';

    public function findByUsername($username)
    {
        return $this->find('/admin/v1/users', '\\DuoAuth\\User', array('username' => $username));
    }

    public function findAll()
    {
        return $this->find('/admin/v1/users', '\\DuoAuth\\User');
    }

    public function preauth($username)
    {
        $request = $this->getRequest('auth')
            ->setPath('/rest/v1/preauth')
            ->setMethod('POST')
            ->setParams(
                array('user' => $username)
            );
        $response = $request->send();

        if ($response->success() == true) {
            return $response->getBody();
        } else {
            return false;
        }
    }

    public function validateCode($code, $device = 'phone1')
    {
        if ($this->username !== null) {
            $request = $this->getRequest('auth')
            ->setPath('/rest/v1/auth')
            ->setMethod('POST')
            ->setParams(
                array(
                    'user'   => $this->username,
                    'factor' => 'passcode',
                    'code'   => $code,
                    'phone'  => $device
                )
            );
            $response = $request->send();
            $body = $response->getBody();
            return ($response->success() == true && $body->result !== 'deny') ? true : false;
        } else {
            return false;
        }
        
    }

    /**
     * Get the phones for the given user ID
     *     NOTE: If user is already fetched and phones exist, those are returned
     *           Otherwise, it either tries to use the user_id or the given $userId
     * 
     * @param $string $userId User ID [optional]
     * @return array List of phones
     */
    public function getPhones($userId = null)
    {
        // if we already have them, return them
        if (!empty($this->phones)) {
            return $this->phones;

        } else {
            $phones = array();
            $userId = ($this->user_id !== null) ? $this->user_id : $userId;

            // we know the user, let's request their phones
            $request = $this->getRequest()
                ->setPath('/admin/v1/users/'.$userId.'/phones');

            $response = $request->send();

            if ($response->success() == true) {
                $phones = $response->getBody();
                foreach ($phones as $index => $phone) {
                    $p = new \DuoAuth\Devices\Phone();
                    $p->load($phone);
                    $phones[$index] = $p;
                }
            }
            return $phones;
        }
    }

    public function associateDevice(\DuoAuth\Device $device)
    {
        if ($device instanceof \DuoAuth\Phone) {
            $request = $this->getRequest()
                ->setMethod('POST')
                ->setParams(array('phone_id' => $device->phone_id))
                ->setPath('/admin/v1/users/'.$this->user_id.'/phones');

        } elseif ($device instanceof \DuoAuth\Token) {
            $request = $this->getRequest()
                ->setMethod('POST')
                ->setParams(array('token_id' => $device->token_id))
                ->setPath('/admin/v1/users/'.$this->user_id.'/tokens');
        }

        $response = $request->send();
        if ($response->success() == true) {
            $body = $response->getBody();
            return (empty($body)) ? true : false;
        } else {
            return false;
        }
    }
}