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

    /**
     * Find the full list of Phones on the account
     * 
     * @return array Set of phone objects
     */
    public function findAll()
    {
        return $this->find('/admin/v1/phones', '\\DuoAuth\\Devices\\Phone');
    }

    /**
     * Find a phone by its internal ID
     * 
     * @param string $phoneId Internal phone ID
     * @return \DuoAuth\Devices\Phone instance
     */
    public function findById($phoneId)
    {
        return $this->find('/admin/v1/phones/'.$phoneId, '\\DuoAuth\\Devices\\Phone');
    }

    /**
     * Associate this Phone with the given user
     * 
     * @param \DuoAuth\User $user User object
     * @param string $phoneId Phone internal ID [optional]
     * @return boolean Pass/fail on association
     */
    public function associate(\DuoAuth\User $user, $phoneId = null)
    {
        $phoneId = ($phoneId !== null) ? $phoneId : $this->phone_id;
        $userId = $user->user_id;

        $request = $this->getRequest()
            ->setMethod('POST')
            ->setParams(array('phone_id' => $phoneId))
            ->setPath('/admin/v1/users/'.$userId.'/phones');

        $response = $request->send();
        if ($response->success() == true) {
            $body = $response->getBody();
            return (empty($body)) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Create and update the Phone object
     * 
     * @return boolean Success/fail of phone update
     */
    public function save()
    {
        $path = ($this->phone_id == null)
            ? '/admin/v1/phones' : '/admin/v1/phones';

        // "number" is required
        if ($this->number == null) {
            return false;
        }

        $params = array(
            'number' => $this->number,
            'name' => $this->name,
            'extension' => $this->extension,
            'type' => $this->type,
            'platform' => $this->platform,
            'predelay' => $this->predelay,
            'postdelay' => $this->postdelay
        );

        $request = $this->getRequest()
            ->setMethod('POST')->setParams($params)->setPath($path);

        $response = $request->send();

        if ($response->success() == true) {
            $body = $response->getBody();
            $this->load($body);
            return (empty($body)) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Delete the Phone device record
     * 
     * @return boolean Success/fail on delete
     */
    public function delete()
    {
        if ($this->phone_id !== null) {
            $request = $this->getRequest()
                ->setMethod('DELETE')
                ->setParams($params)
                ->setPath('/admin/v1/phones/'.$this->phone_id);

            $response = $request->send();
            $body = $response->getBody();

            return ($response->success() == true && $body == '') ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Send activation message to the Phone
     * 
     * @return boolean Success/fail on send
     */
    public function smsActivation()
    {
        if ($this->phone_id !== null) {

            $request = $this->getRequest()
                ->setMethod('POST')
                ->setPath('/admin/v1/phones/'.$this->phone_id.'/send_sms_activation');

            $response = $request->send();

            return ($response->success() == true) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Send SMS passcodes to the Phone
     * 
     * @return boolean Success/fail on send
     */
    public function smsPasscode()
    {
        if ($this->phone_id !== null) {

            $request = $this->getRequest()
                ->setMethod('POST')
                ->setPath('/admin/v1/phones/'.$this->phone_id.'/send_sms_passcodes');

            $response = $request->send();
            $body = $response->getBody();

            return ($response->success() == true && $body == '') ? true : false;
        } else {
            return false;
        }
    }
}
