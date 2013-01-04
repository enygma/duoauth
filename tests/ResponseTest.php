<?php

namespace DuoAuth;

require_once 'MockClient.php';
require_once 'MockResponse.php';

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    private $response = null;

    public function setUp()
    {
        $this->response = new \DuoAuth\Response();
    }

    public function testValidDataSet()
    {
        $content = 'testing';
        $request = new \DuoAuth\Request(new MockClient());

        $data = json_encode(array(
            'response' => $content
        ));
        $response = new MockResponse();
        $response->setBody($data);

        $this->response->setData($response);
        $this->assertEquals(
            $content,
            $this->response->getBody()
        );
    }

}