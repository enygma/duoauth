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

    public function find($params = null)
    {
        $request = $this->getRequest()
            ->setPath('/admin/v1/users');

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
                        $u = new \DuoAuth\User();
                        $u->load($user);
                        $users[$index] = $u;
                    }
                    return $users;
                }
            }
        } else {
            return false;
        }
    }

    public function findByUsername($username)
    {
        return $this->find(array('username' => $username));
    }

    public function findAll()
    {
        return $this->find();
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
}