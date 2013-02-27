<?php

namespace DuoAuth;

require_once 'MockModel.php';
require_once 'MockClient.php';
require_once 'MockResponse.php';

class BaseModelHelper extends \PHPUnit_Framework_TestCase
{
    protected function buildMockRequest($data)
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

    protected function buildMockModel($modelClass, $request, $properies = array())
    {
        $user = $this->getMock($modelClass, array('getRequest'));

        $user->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        foreach ($properies as $propertyName => $propertyValue) {
            $user->$propertyName = $propertyValue;
        }

        return $user;
    }
}