<?php

namespace DuoAuth\Devices;

class Token extends \DuoAuth\Device
{
    protected $properties = array(
        'token_id' => array(
            'type' => 'string'
        ),
        'type' => array(
            'type' => 'string'
        ),
        'serial' => array(
            'type' => 'string'
        ),
        'secret' => array(
            'type' => 'string'
        ),
        'private_id' => array(
            'type' => 'string'
        ),
        'aes_key' => array(
            'type' => 'string'
        ),
        'users' => array(
            'type' => 'array'
        )
    );

    protected $integration = 'admin';

    /**
     * Find the full list of Tokens on the account
     *
     * @return array Set of token objects
     */
    public function findAll()
    {
        return $this->find('/admin/v1/tokens', '\\DuoAuth\\Devices\\Token');
    }

    /**
     * Find a token by its internal ID
     *
     * @param string $tokenId Internal token ID
     * @return \DuoAuth\Devices\Token instance
     */
    public function findById($tokenId)
    {
        return $this->find('/admin/v1/tokens/'.$tokenId, '\\DuoAuth\\Devices\\Token');
    }

    /**
     * Delete the Token device record
     *
     * @return boolean Success/fail on delete
     */
    public function delete($tokenId = null)
    {
        $tokenId = ($tokenId !== null) ? $tokenId : $this->token_id;
        if ($tokenId === null) {
            throw new \InvalidArgumentException('Token ID cannot be null');
        }
        $params = array('token_id' => $tokenId);

        $request = $this->getRequest('admin')
            ->setMethod('DELETE')
            ->setPath('/admin/v1/tokens/'.$tokenId);

        $response = $request->send();
        $body = $response->getBody();

        return ($response->success() == true && $body == '') ? true : false;
    }

}
