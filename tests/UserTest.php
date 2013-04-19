<?php

namespace DuoAuth;

class UserTest extends BaseModelHelper
{
    /**
     * Test that the "find all" returns results and that they're the right type
     * @covers \DuoAuth\User::findAll
     */
    public function testFindAllValidResults()
    {

        $user = new \DuoAuth\User();
        $results = array(
            "response" => array(
                array("username" => "testuser1"),
                array("username" => "testuser2")
            )
        );
        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $result = $user->findAll();
        $this->assertEquals(2, count($result));
        foreach ($result as $r) {
            if (!($r instanceof \DuoAuth\User)) {
                $this->fail('Invalid object type returned - not a User');
            }
        }
    }

    /**
     * Validate code from the user (a valid response)
     * @covers \DuoAuth\User::validateCode
     */
    public function testValidateCodeValid()
    {
        $code = 'testing1234';
        $results = array('response' => array('result' => 'allow'));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $v = $user->validateCode($code, 'ccornutt');
        $this->assertTrue($v);
    }

    /**
     * Try to request too many auth codes (max 10)
     * @covers \DuoAuth\User::generateBypassCodes
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateCodesTooMany()
    {
        $user = new \DuoAuth\User();
        $user->generateBypassCodes('testuser1', 20);
    }

    /**
     * Test a correct response to the successful code request
     * @covers \DuoAuth\User::generateBypassCodes
     */
    public function testGenerateCodesSuccess()
    {
        $codeList = array(
            '12345','67890',
            '09876','54321'
        );
        $results = array('response' =>
            array('codes' => $codeList)
        );

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $return = $user->generateBypassCodes('testuser1');
        $this->assertEquals($codeList, $return->codes);
    }

    /**
     * Try to request too many auth codes (max 10)
     * @covers \DuoAuth\User::generateBypassCodes
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateCodesNaN()
    {
        $user = new \DuoAuth\User();
        $user->generateBypassCodes('testuser1', '10');
    }

    /**
     * Test the response for a successful enrollment status
     * @covers \DuoAuth\User::getEnrollStatus
     */
    public function testGetEnrollStatusValid()
    {
        $userId = 1234;
        $activationCode = 'testcode1';
        $results = array('response' => 'success');

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $status = $user->getEnrollStatus($userId, $activationCode);
        $this->assertEquals('success', $status);
    }

    /**
     * Test the response when a user is validly enrolled
     * @covers \DuoAuth\User::enroll
     */
    public function testEnrollValidUser()
    {
        $username = 'testuser';
        $results = array('response' => array(
            'username' => $username
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $return = $status = $user->enroll($username);
        $this->assertEquals($return->username, $username);
    }

    /**
     * Test the call to enroll with # of seconds for it to be valid
     * @covers \DuoAuth\User::enroll
     */
    public function testEnrollSecondsValid()
    {
        $username = 'testuser';
        $results = array('response' => array(
            'username' => $username
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $return = $status = $user->enroll($username, 20);
        $this->assertEquals($return->username, $username);
    }

    /**
     * Test that the sending of an SMS returns the code
     * @covers \DuoAuth\User::sendSms
     * @covers \DuoAuth\User::getLastPin
     */
    public function testSendSmsValid()
    {
        $pin = '12345';
        $results = array('response' => array(
            'pin' => $pin
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $status = $user->sendSms('2145551234');
        $this->assertEquals($user->getLastPin(), $pin);
    }

    /**
     * Test that the sending of a verification returns a PIN
     * @covers \DuoAuth\User::sendVerification
     * @covers \DuoAuth\User::getLastPin
     */
    public function testSendVerificationValid()
    {
        $pin = '12345';
        $results = array('response' => array(
            'pin' => $pin
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $return = $user->sendVerification('call','2145551234');
        $this->assertEquals($return->pin, $pin);
    }

    /**
     * Test that the sending of an SMS returns the code
     * @covers \DuoAuth\User::sendCall
     * @covers \DuoAuth\User::getLastPin
     */
    public function testSendPhoneValid()
    {
        $pin = '12345';
        $results = array('response' => array(
            'pin' => $pin
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $status = $user->sendCall('2145551234');
        $this->assertEquals($user->getLastPin(), $pin);
    }

    /**
     * Test that a send push returns correctly for an "allow"
     * @covers \DuoAuth\User::sendPush
     */
    public function testSendPushValid()
    {
        $results = array('response' => array(
            'result' => 'allow'
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $status = $user->sendPush('12345', 'testuser1');
        $this->assertTrue($status);
    }

    /**
     * Test the response of a successful delete
     * @covers \DuoAuth\User::delete
     */
    public function testDeleteSuccess()
    {
        $username = 'testuser2';
        $results = array('response' => '');

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);
        $user->user_id = '12345';

        $status = $user->delete();
        $this->assertTrue($status);
    }

    /**
     * Test that save works and populates the user correctly
     * @covers \DuoAuth\User::save
     */
    public function testSaveSuccess()
    {
        $username = 'testuser2';
        $results = array('response' => array(
            'username' => $username
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $status = $user->save();
        $this->assertEquals($user->username, $username);
    }

    /**
     * If phones are already set, return them right away
     * @covers \DuoAuth\User::getPhones
     */
    public function testGetSetPhones()
    {
        $phones = array(
            array('name' => 'Phone #1')
        );
        $user = new \DuoAuth\User();
        $user->phones = $phones;

        $this->assertEquals($phones, $user->getPhones());
    }

    /**
     * Test the throwing of an exception when no user ID is set
     * @covers \DuoAuth\User::getPhones
     * @expectedException \InvalidArgumentException
     */
    public function testGetPhonesInvalidUser()
    {
        $user = new \DuoAuth\User();
        $user->getPhones();
    }

    /**
     * Test that the phone response is translated into objects correctly
     * @covers \DuoAuth\User::getPhones
     */
    public function testGetPhonesList()
    {
        $results = array('response' => array(
            array('number' => '2145551234'),
            array('number' => '9725551234')
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $phones = $user->getPhones('1234');

        $this->assertTrue($phones[0] instanceof \DuoAuth\Devices\Phone);
        $this->assertEquals($phones[1]->number, '9725551234');
    }

    /**
     * Test that a device correctly associated with a user
     * @covers \DuoAuth\User::associateDevice
     */
    public function testAssociateDevice()
    {
        $phone1 = new \DuoAuth\Devices\Phone();
        $phone1->number = '2145551234';

        $results = array('response' => '');

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request, array('id' => '1234'));

        $result = $user->associateDevice($phone1);
        $this->assertTrue($result);
    }

    /**
     * Test that a device correctly associated with a user
     * @covers \DuoAuth\User::unassociateDevice
     */
    public function testUnassociateDevice()
    {
        $phone1 = new \DuoAuth\Devices\Phone();
        $phone1->number = '2145551234';

        $results = array('response' => '');

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request, array('id' => '1234'));

        $result = $user->unassociateDevice($phone1);
        $this->assertTrue($result);
    }

    /**
     * Test that a valid response is returned from a preauth request
     * @covers \DuoAuth\User::preauth
     */
    public function testPreauthValid()
    {
        $response =  (object)array(
            'result' => 'auth',
            'factors' => (object)array(
                'default' => 'phone1'
            )
        );
        $results = array('response' => $response);

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockModel('\DuoAuth\User', $request);

        $result = $user->preauth('testuser1');
        $this->assertEquals($result, $response);
    }

    /**
     * Test the getter/setter for the last pin returned
     * @covers \DuoAuth\User::setLastPin
     * @covers \DuoAuth\User::getLastPin
     */
    public function testGetSetLastPin()
    {
        $pin = '1234';
        $user = new \DuoAuth\User();
        $user->setLastPin($pin);

        $this->assertEquals($pin, $user->getLastPin());
    }
}

?>