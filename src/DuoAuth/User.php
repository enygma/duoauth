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
        ),
        'last_pin_sent' => array(
            'type' => 'string'
        )
    );

    protected $integration = 'admin';

    /**
     * Find a single user by username
     *
     * @param string $username Username to search for
     * @return boolean Pass/fail on find
     */
    public function findByUsername($username)
    {
        return $this->find('/admin/v1/users', '\\DuoAuth\\User', array('username' => $username));
    }

    /**
     * Get a list of all users on the account
     *
     * @return array Set of users (\DuoAuth\User)
     */
    public function findAll()
    {
        return $this->find('/admin/v1/users');
    }

    /**
     * Preauth the username given
     *
     * @param string $username Username to preauth
     * @return mixed|boolean Response body or false on fail
     */
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

    /**
     * Validate the code given by the user
     *
     * @param string $code User-inputted code
     * @param string $device Device name (internal) [optional]
     * @return boolean Pass/fail on validation
     */
    public function validateCode($code, $username = null, $device = 'phone1')
    {
        if ($this->username == null && $username == null) {
            return false;
        } else {
            $username = ($username !== null) ? $username : $this->username;

            $request = $this->getRequest('auth')
            ->setPath('/rest/v1/auth')
            ->setMethod('POST')
            ->setParams(
                array(
                    'user'   => $username,
                    'factor' => 'passcode',
                    'code'   => $code,
                    'phone'  => $device
                )
            );
            $response = $request->send();
            $body = $response->getBody();
            return ($response->success() == true && $body->result !== 'deny') ? true : false;
        }
        return false;
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
            $request = $this->getRequest('admin')
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

    /**
     * Associate a device to the user
     *
     * @param \DuoAuth\Device $device Device to associate
     * @return boolean Pass/fail on association
     */
    public function associateDevice(\DuoAuth\Device $device)
    {
        if ($device instanceof \DuoAuth\Phone) {
            $request = $this->getRequest('admin')
                ->setMethod('POST')
                ->setParams(array('phone_id' => $device->phone_id))
                ->setPath('/admin/v1/users/'.$this->user_id.'/phones');

        } elseif ($device instanceof \DuoAuth\Token) {
            $request = $this->getRequest('admin')
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

    /**
     * Save the current user (supports new user creation)
     *
     * @return boolean Pass/fail of save
     */
    public function save()
    {
        $path = ($this->user_id == null)
            ? '/admin/v1/users' : '/admin/v1/users/'.$this->user_id;

        $params = array(
            'username' => $this->username,
            'realname' => $this->realname,
            'status' => 'active'
        );

        $request = $this->getRequest('admin')
            ->setMethod('POST')
            ->setParams($params)
            ->setPath($path);

        $response = $request->send();

        if ($response !== null && $response->success() == true) {
            $body = $response->getBody();
            $this->load($body);
            return (empty($body)) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Delete the current user
     *
     * @return boolean Success/fail of user delete
     */
    public function delete()
    {
        if ($this->user_id !== null) {
            $request = $this->getRequest('admin')
                ->setMethod('DELETE')
                ->setPath('/admin/v1/users/'.$this->user_id);

            $response = $request->send();
            $body = $response->getBody();

            return ($response->success() == true && $body == '') ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Send a push login request to the user's device
     *     NOTE: Request waits for user to approve to finish (or timeout)
     *
     * @param string $device Identifier for user device (default "phone1") [optional]
     * @param string $username Username to send request to [optional]
     * @param array $addlInfo Additional info to send with the push [optional]
     * @return boolean Success/fail of request
     */
    public function sendPush($username = null, $device = 'phone1', $addlInfo = null)
    {
        if ($this->username == null && $username == null) {
            return false;
        } else {
            $username = ($username !== null) ? $username : $this->username;

            $params = array(
                'user'   => $this->username,
                'factor' => 'push',
                'phone'  => $device
            );

            if ($addlInfo !== null && is_array($addlInfo)) {
                $params['pushinfo'] = http_build_query($addlInfo);
            }

            $request = $this->getRequest('auth')
                ->setPath('/rest/v1/auth')
                ->setMethod('POST')
                ->setParams($params);

            $response = $request->send();
            $body = $response->getBody();

            return ($response->success() == true && $body->result !== 'deny') ? true : false;
        }
    }

    /**
     * Send a verification message to a phone number
     *     NOTE: Sending uses a Telephony credit on your account
     *
     * @param string $type of Verification connection
     * @param string $phoneNumber Phone number to send message
     * @param string $message Message to send [optional]
     * @param array $addlInfo Additional settings to pass to the API call
     * @return boolean|string False if failure, Body contents on success
     */
    public function sendVerification($type, $phoneNumber, $message = null, $addlInfo = null)
    {
        if ($message == null) {
            $message = 'Your valid PIN is <pin>';
        }
        $params = array(
            'phone' => $phoneNumber,
            'message' => $message
        );
        if ($addlInfo !== null && is_array($addlInfo)) {
            $params = array_merge($params, $addlInfo);
        }

        $request = $this->getRequest('verify')
            ->setPath('/verify/v1/'.strtolower($type))
            ->setMethod('POST')
            ->setParams($params);

        $response = $request->send();
        $body = $response->getBody();

        return ($response->success() == true) ? $body : false;
    }

    /**
     * Send a verification call to the phone number given
     *     When call is made, user object is updated with the PIN sent/generated in last_pin_sent
     *
     * @param string $phoneNumber Phone number to call
     * @param string $message Message to send/say [optional]
     * @param array $addlInfo Additional settings to send to the API
     * @return boolean False on failure, true on success
     */
    public function sendCall($phoneNumber, $message = null, $addlInfo = null)
    {
        $result = $this->sendVerification('call', $phoneNumber, $message, $addlInfo);
        if ($result == false) {
            return false;
        }

        $this->last_pin_sent = $result->pin;
        return true;
    }

    /**
     * Send a verification SMS to the phone number given
     *     When SMS is sent, user object is updated with the PIN sent/generated in last_pin_sent
     *
     * @param string $phoneNumber Phone number to call
     * @param string $message Message to send/say [optional]
     * @param array $addlInfo Additional settings to send to the API
     * @return boolean False on failure, true on success
     */
    public function sendSms($phoneNumber, $message = null, $addlInfo = null)
    {
        $result = $this->sendVerification('sms', $phoneNumber, $message, $addlInfo);
        if ($result == false) {
            return false;
        }

        $this->last_pin_sent = $result->pin;
        return true;
    }

    /**
     * Enroll a user in your account by username
     * 
     * @param string $username Username to add
     * @param integer $valid Number of seconds for the generated code to be valid
     * @return boolean|array False if fails, array with user data otherwise
     */
    public function enroll($username = null, $valid = null)
    {
        $params = array();
        if ($valid !== null && is_int($valid)) {
            $params['valid_secs'] = $valid;
        }
        if ($username !== null) {
            $params['username'] = $username;
        }

        $request = $this->getRequest('auth')
            ->setPath('/auth/v2/enroll')
            ->setMethod('POST')
            ->setParams($params);

        $response = $request->send();
        $body = $response->getBody();

        return ($response->success() == true) ? $body : false;
    }

    /**
     * Get the enrollment status for a user
     *     Returns either "success", "waiting" or "invalid"
     * 
     * @param string $userId User ID returned from "enroll" call (not username)
     * @param string $activationCode Activation code returned from enroll (with "duo://")
     * @return string|boolean False if request failed, return string otherwise
     */
    public function getEnrollStatus($userId, $activationCode)
    {
        $params = array(
            'user_id' => $userId,
            'activation_code' => $activationCode
        );

        $request = $this->getRequest('auth')
            ->setPath('/auth/v2/enroll_status')
            ->setMethod('POST')
            ->setParams($params);

        $response = $request->send();
        $body = $response->getBody();

        return ($response->success() == true) ? $body : false;
    }
}