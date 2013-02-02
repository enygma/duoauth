<?php

namespace DuoAuth\Account;

/**
 * As this API (Accounts) is in beta, these methods have not been tested
 */
class Child extends \DuoAuth\Model
{
    protected $properties = array(
        'account_id' => array(
            'type' => 'string'
        ),
        'name' => array(
            'type' => 'string'
        )
    );

    public function save()
    {
        $path = ($this->account_id == null)
            ? '/accounts/v1/account/create' : '/accounts/v1/account/'.$this->account_id;

        $params = array(
            'name' => $this->name
        );

        $request = $this->getRequest('account')
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

    public function delete()
    {
        if ($this->account_id !== null) {
            $request = $this->getRequest('account')
                ->setMethod('POST')
                ->setData(array('account_id' => $this->account_id))
                ->setPath('/accounts/v1/account/delete');

            $response = $request->send();
            $body = $response->getBody();

            return ($response->success() == true && $body == '') ? true : false;
        } else {
            return false;
        }
    }
}