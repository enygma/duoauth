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

    /**
     * Create/update a token
     * 
     * @param array $params Token parameters to send
     * @return boolean Success/fail of save
     */
    public function saveToken($params)
    {
        $path = (!isset($params['token_id']) || $params['token_id'] == null)
            ? '/admin/v1/tokens' : '/admin/v1/tokens/'.$params['token_id'];

        $request = $this->getRequest('admin')
            ->setMethod('POST')->setParams($params)->setPath($path);

        $response = $request->send();

        if ($response->success() == true) {
            $body = $response->getBody();
            return $this->load($body);
        } else {
            return false;
        }
    }

    /**
     * Resync a token when given three codes
     * 
     * @param array $codes Token codes
     * @param string $tokenId Internal token ID [optional]
     * @throws \InvalidArgumentException Bad codes list or invalid token ID
     * @return boolean Success/fail of request
     */
    public function resync($codes, $tokenId = null)
    {
        if (!is_array($codes) || count($codes)<3) {
            throw new \InvalidArgumentException('You must provide a valid set of 3 codes');
        }

        $tokenId = ($tokenId !== null) ? $tokenId : $this->token_id;
        if ($tokenId == null) {
            throw new \InvalidArgumentException('Invalid token ID');
        }

        $path = '/admin/v1/tokens/'.$tokenId.'/resync';
        $request = $this->getRequest('admin')
            ->setMethod('POST')->setParams($codes)->setPath($path);

        $response = $request->send();
        return ($response->success() == true && $body == '') ? true : false;
    }

}
