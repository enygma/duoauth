<?php

namespace DuoAuth;

require_once 'MockClient.php';
require_once 'MockResponse.php';

class UserTest extends \PHPUnit_Framework_TestCase
{
    private function buildMockRequest($data)
    {
        $mockClient = new MockClient();
        $response = new \DuoAuth\Response();

        $r = new MockResponse();
        $r->setBody(json_encode($data));
        $response->setData($r);

        $request = $this->getMock('\DuoAuth\Request', array('send'), array($mockClient));

        $request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        return $request;
    }

    private function buildMockUser($request)
    {
        $user = $this->getMock('\DuoAuth\User', array('getRequest'));

        $user->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $user;
    }

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
        $user = $this->buildMockUser($request);

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
        $user = $this->buildMockUser($request);

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
        $user = $this->buildMockUser($request);

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
        $user = $this->buildMockUser($request);

        $status = $user->getEnrollStatus($userId, $activationCode);
        $this->assertEquals('success', $status);
    }

    public function testEnrollValidUser()
    {
        $username = 'testuser';
        $results = array('response' => array(
            'username' => $username
        ));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockUser($request);

        $return = $status = $user->enroll($username);
        $this->assertEquals($return->username, $username);
    }

    /**
     * If phones are already set, return them right away
     * @covers \DuoAuth\User::getphones
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
}

?>