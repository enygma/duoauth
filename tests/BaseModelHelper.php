<?php

namespace DuoAuth;

require_once 'MockModel.php';
require_once 'MockClient.php';
require_once 'MockResponse.php';

class BaseModelHelper extends \PHPUnit_Framework_TestCase
{
    /**
     * Build a mock request with the given data
     *
     * @param array $data Data to set in the response
     * @param \DuoAuth\Response $response Mocked Response object [optional]
     * @param mixed $methods Moethods to add to the mock object
     * @return mocked Request object
     */
    protected function buildMockRequest($data, $response = null, $methods = null)
    {
        $mockMethods = array('send');
        if (is_array($methods)) {
            $mockMethods = array_merge($methods, $mockMethods);
        }
        if ($methods === false) {
            $mockMethods = array();
        }

        $mockClient = new MockClient();
        $response = ($response !== null) ? $response : $this->buildMockResponse($data);
        $request = $this->getMock('\DuoAuth\Request', $mockMethods, array($mockClient));

        if (!empty($mockMethods)) {
            $request->expects($this->once())
                ->method('send')
                ->will($this->returnValue($response));
        }

        return $request;
    }

    /**
     * Build a mock response object to inject into the response
     *
     * @param  array $data Data to set in the response
     * @return \DuoAuth\Response object
     */
    protected function buildMockResponse($data)
    {
        $response = new \DuoAuth\Response();
        $r = new MockResponse();

        $r->setBody(json_encode($data));
        $response->setData($r);

        return $response;
    }

    /**
     * Build a mock of the expected model
     *
     * @param string $modelClass Full classname to mock (model-based)
     * @param object $request Mocked \DuoAuth\Response instance
     * @param array $properies Additonal properties to set on the model object
     * @return Mocked model-based object
     */
    protected function buildMockModel($modelClass, $request, $properies = array())
    {
        $mock = $this->getMock($modelClass, array('getRequest'));

        $mock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        foreach ($properies as $propertyName => $propertyValue) {
            $mock->$propertyName = $propertyValue;
        }

        return $mock;
    }
}