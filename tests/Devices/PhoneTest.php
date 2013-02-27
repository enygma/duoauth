<?php

namespace DuoAuth;

class PhoneTest extends BaseModelHelper
{
    /**
     * Be sure it throws an exception when no Phone ID can be found
     * @covers \DuoAuth\Devices\Phone::associate
     * @expectedException \InvalidArgumentException
     */
    public function testAssociateDeviceNoPhoneId()
    {
        $phone = new \DuoAuth\Devices\Phone();
        $user = new \DuoAuth\User();
        $phone->associate($user);
    }

    /**
     * Test the successful association of a device
     * @covers \DuoAuth\Devices\Phone::associate
     */
    public function testAssociateDeviceSuccess()
    {
        $results = array('response' => '');
        $phoneId = '12345';

        $request = $this->buildMockRequest($results);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);

        $user = new \DuoAuth\User();
        $result = $phone->associate($user, $phoneId);

        $this->assertTrue($result);
    }

    /**
     * Be sure it throws an exception when no Phone ID can be found
     * @covers \DuoAuth\Devices\Phone::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteDeviceNoPhoneId()
    {
        $phone = new \DuoAuth\Devices\Phone();
        $phone->delete();
    }

    /**
     * Test the successful deletion of a device
     * @covers \DuoAuth\Devices\Phone::delete
     */
    public function testDeleteDeviceSuccess()
    {
        $results = array('response' => '');
        $phoneId = '12345';

        $request = $this->buildMockRequest($results);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);

        $user = new \DuoAuth\User();
        $result = $phone->delete($phoneId);

        $this->assertTrue($result);
    }

    /**
     * Test that, when the "success" of the request is bad, return is false
     * @covers \DuoAuth\Devices\Phone::delete
     */
    public function testDeleteDeviceFail()
    {
        $results = array('response' => '');
        $phoneId = '12345';

        $response = $this->buildMockResponse($results);
        $response->setSuccess(false);

        $request = $this->buildMockRequest($results, $response);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);

        $user = new \DuoAuth\User();
        $result = $phone->delete($phoneId);

        $this->assertFalse($result);
    }
}
