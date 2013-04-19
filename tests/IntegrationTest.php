<?php

namespace DuoAuth;

class IntegrationTest extends BaseModelHelper
{
    /**
     * Test the getter/setter for the secret key
     * @covers \DuoAuth\Integration::getSecret
     * @covers \DuoAuth\Integration::setSecret
     */
    public function testGetSetSecret()
    {
        $secret = '12345abcdef';
        $config = array();
        $int = new \DuoAuth\Integration($config);

        $int->setSecret($secret);
        $this->assertEquals($int->getSecret(), $secret);
    }

    /**
     * Test the getter/setter for the integration key
     * @covers \DuoAuth\Integration::getIntegration
     * @covers \DuoAuth\Integration::setIntegration
     */
    public function testGetSetIntegration()
    {
        $integration = '12345abcdef';
        $config = array();
        $int = new \DuoAuth\Integration($config);

        $int->setIntegration($integration);
        $this->assertEquals($int->getIntegration(), $integration);
    }

    /**
     * Test the getter/setter for the hostname
     * @covers \DuoAuth\Integration::getHostname
     * @covers \DuoAuth\Integration::setHostname
     */
    public function testGetSetHostname()
    {
        $hostname = 'testhost.com';
        $config = array();
        $int = new \DuoAuth\Integration($config);

        $int->setHostname($hostname);
        $this->assertEquals($hostname, $int->getHostname());
    }

    /**
     * Test that a successful save behaves correctly
     * @covers \DuoAuth\Integration::save
     */
    public function testSaveIntegrationSuccess()
    {
        $greeting = 'howdy';
        $results = array('response' => array(
            'greeting' => $greeting
        ));

        $request = $this->buildMockRequest($results);
        $int = $this->buildMockModel('\DuoAuth\Integration', $request);

        $result = $int->save();
        $this->assertTrue($result);
        $this->assertEquals($int->greeting, $greeting);
    }

    /**
     * Test that a failed save behaves correctly
     * @covers \DuoAuth\Integration::save
     */
    public function testSaveIntegrationFail()
    {
        $greeting = 'howdy';
        $results = array('response' => array(
            'greeting' => $greeting
        ));

        $response = $this->buildMockResponse($results);
        $response->setSuccess(false);

        $request = $this->buildMockRequest($results, $response);
        $int = $this->buildMockModel('\DuoAuth\Integration', $request);

        $result = $int->save();
        $this->assertFalse($result);
        $this->assertEquals($int->greeting, null);
    }

    /**
     * Test the getter/setter for the integration alias
     * @covers \DuoAuth\Integration::setAlias
     * @covers \DuoAuth\Integration::getAlias
     */
    public function testGetSetAlias()
    {
        $alias = 'testalias';
        $config = array();

        $int = new \DuoAuth\Integration($config);
        $int->setAlias($alias);
        $this->assertEquals($int->getAlias(), $alias);
    }
}
