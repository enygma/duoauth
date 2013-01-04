<?php

namespace DuoAuth;

require_once 'MockClient.php';

class RequestTest extends \PHPUnit_Framework_TestCase
{
    private $request = null;

    public function setUp()
    {
        $client = new MockClient();
        $this->request = new \DuoAuth\Request($client);
    }

    public function testRequestSetSecret()
    {
        $this->request->setSecretKey('test');
        $this->assertEquals(
            'test',
            $this->request->getSecretKey()
        );
    }
}