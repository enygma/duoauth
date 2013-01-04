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

    /**
     * Test that the data is set correctly on the response
     */
    public function testValidDataSet()
    {
        $content = 'testing';
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

    /**
     * Test that, when the object is given on construct, data is
     *     still set correctly.
     */
    public function testSetDataOnConstruct()
    {
        $data = json_encode(array(
            'response' => 'test'
        ));
        $response = new MockResponse();
        $response->setBody($data);

        $r = new \DuoAuth\Response($response);
        $this->assertEquals('test', $r->getBody());
    }

    /**
     * Test that the "success" returns correctly (our mock is set to 200, hard-coded)
     */
    public function testSuccessCorrectlySet()
    {
        $content = 'testing';
        $data = json_encode(array(
            'response' => $content
        ));

        $response = new MockResponse();
        $response->setBody($data);

        $this->response->setData($response);
        $this->assertEquals(true, $this->response->success());
    }
}