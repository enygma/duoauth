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
     * Test the unsuccessful association of a device
     * @covers \DuoAuth\Devices\Phone::associate
     */
    public function testAssociateDeviceFail()
    {
        $results = array('response' => '');
        $phoneId = '12345';

        $response = $this->buildMockResponse($results);
        $response->setSuccess(false);

        $request = $this->buildMockRequest($results, $response);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);

        $user = new \DuoAuth\User();
        $result = $phone->associate($user, $phoneId);

        $this->assertFalse($result);
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

    /**
     * Test a successful save on a Phone device
     * @covers \DuoAuth\Devices\Phone::save
     */
    public function testCreateDeviceSuccess()
    {
        $phoneNumber = '2145551234';
        $results = array('response' => array(
            'number' => $phoneNumber
        ));

        $request = $this->buildMockRequest($results);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);

        $phone->number = $phoneNumber;
        $result = $phone->save();

        $this->assertTrue($result);
    }

    /**
     * Test a successful save on a Phone device
     * @covers \DuoAuth\Devices\Phone::save
     */
    public function testCreateDeviceFail()
    {
        $phoneNumber = '2145551234';
        $results = array('response' => array(
            'number' => $phoneNumber
        ));

        $response = $this->buildMockResponse($results);
        $response->setSuccess(false);

        $request = $this->buildMockRequest($results, $response);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);

        $phone->number = $phoneNumber;
        $result = $phone->save();

        $this->assertFalse($result);
    }

    /**
     * Test that a save throws an exception when there's no "number" property
     * @covers \DuoAuth\Devices\Phone::save
     * @expectedException \InvalidArgumentException
     */
    public function testCreateDeviceNoNumber()
    {
        $phone = new \DuoAuth\Devices\Phone();
        $phone->save();
    }

    /**
     * Test that the sending of an SMS request is successful
     * @covers \DuoAuth\Devices\Phone::smsActivation
     */
    public function testSendSmsSuccess()
    {
        $results = array('response' => array(
            'activation_msg' => 'This is the activation message',
            'installation_msg' => 'This is the installation message',
            'valid_secs' => 3600
        ));

        $request = $this->buildMockRequest($results);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);
        $phone->phone_id = '12345';

        $result = $phone->smsActivation();
        $this->assertTrue($result);
    }

    /**
     * Test that a SMS send fails without the "phone ID"
     * @covers \DuoAuth\Devices\Phone::smsActivation
     * @expectedException \InvalidArgumentException
     */
    public function testSendSmsFailNoPhoneID()
    {
        $phone = new \DuoAuth\Devices\Phone();
        $phone->smsActivation();
    }

    /**
     * Test that the sending of an SMS request is successful
     * @covers \DuoAuth\Devices\Phone::smsPasscode
     */
    public function testSendPasscodeSuccess()
    {
        $results = array('response' => '');

        $request = $this->buildMockRequest($results);
        $phone = $this->buildMockModel('\DuoAuth\Devices\Phone', $request);
        $phone->phone_id = '12345';

        $result = $phone->smsPasscode();
        $this->assertTrue($result);
    }

    /**
     * Test that a SMS send fails without the "phone ID"
     * @covers \DuoAuth\Devices\Phone::smsPasscode
     * @expectedException \InvalidArgumentException
     */
    public function testSendPasscodeFailNoPhoneID()
    {
        $phone = new \DuoAuth\Devices\Phone();
        $phone->smsPasscode();
    }
}
