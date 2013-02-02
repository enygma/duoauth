<?php

namespace DuoAuth;

/**
 * As this API (Accounts) is in beta, these methods have not been tested
 */
class Account extends \DuoAuth\Model
{
    protected $properties = array(
        'children' => array(
            'type' => 'array'
        )
    );

    /**
     * Get the current account information
     *     (Right now, just child accounts)
     *
     * @return \DuoAuth\Account instance
     */
    public function get()
    {
        $this->children = $this->getChildren();
        return $this;
    }

    /**
     * Get child accounts for current account
     *
     * @return array Set of child accounts
     */
    public function getChildren()
    {
        $request = $this->getRequest('account')
            ->setPath('/accounts/v1/account/list');

        $response = $request->send();

        if ($response->success() == true) {
            $body = $response->getBody();
            $children = array();
            foreach ($body->response as $child) {
                $c = new \DuoAuth\Account\Child();
                $c->values($child);
                $children[] = $c;
            }
            return $children;
        } else {
            return false;
        }
    }
}