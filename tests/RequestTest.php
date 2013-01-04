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

    /**
     * Test get/set of secret key
     */
    public function testRequestSetSecretKey()
    {
        $this->request->setSecretKey('test');
        $this->assertEquals(
            'test',
            $this->request->getSecretKey()
        );
    }

    /**
     * Test the get/set of integration key
     */
    public function testRequestSetIntKey()
    {
        $this->request->setIntKey('test');
        $this->assertEquals(
            'test',
            $this->request->getIntKey()
        );
    }

    /**
     * Test get/set of api hostname
     */
    public function testRequestSetApiHostname()
    {
        $this->request->setHostname('test');
        $this->assertEquals(
            'test',
            $this->request->getHostname()
        );
    }

    /**
     * Test the get/set of HTTP method
     */
    public function testRequestSetHttpMethod()
    {
        $this->request->setMethod('POST');
        $this->assertEquals(
            'POST',
            $this->request->getMethod()
        );
    }

    /**
     * Test the get/set of the path for request
     */
    public function testRequestSetPathDefault()
    {
        $this->request->setPath('/foo/bar');
        $this->assertEquals(
            '/foo/bar.json',
            $this->request->getPath()
        );
    }

    /**
     * Test the get/set of request path with custom extension
     */
    public function testRequestSetPathXml()
    {
        $this->request->setPath('/foo/bar', 'xml');
        $this->assertEquals(
            '/foo/bar.xml',
            $this->request->getPath()
        );
    }

    /**
     * Test the get/set of parameters on the request
     */
    public function testRequestSetParams()
    {
        $params = array('test' => true);
        $this->request->setParams($params);
        $this->assertEquals(
            $params,
            $this->request->getParams()
        );
    }

    /**
     * Test the get/set of additional options to add to the hash
     */
    public function testRequestSetHashOptions()
    {
        $params = array('Date' => date('r'));
        $this->request->setHashOptions($params);
        $this->assertEquals(
            $params,
            $this->request->getHashOptions()
        );
    }

    /**
     * Test the get/set of additonal parameters on request
     */
    public function testRequestSetParamsAdditional()
    {
        $params = array('test' => true);
        $this->request->setParams($params);
        $this->assertEquals(
            $params,
            $this->request->getParams()
        );
        // add another
        $this->request->setParam('foo', 'bar');
        $this->assertEquals(
            array('test' => true, 'foo' => 'bar'),
            $this->request->getParams()
        );
    }

    /**
     * Test that the client fetched is valid and the right type
     */
    public function testGetClient()
    {
        $client = $this->request->getClient();
        $this->assertTrue(
            $client !== null && $client instanceof MockClient
        );
    }

}
