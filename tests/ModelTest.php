<?php

namespace DuoAuth;

require_once 'MockModel.php';
require_once 'MockClient.php';
require_once 'MockResponse.php';

class ModelTest extends \PHPUnit_Framework_TestCase
{
    private $model = null;

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

    public function setUp()
    {
        $this->model = new \DuoAuth\Model();
    }

    /**
     * Test that the properties are set correctly based on results
     * @covers \DuoAuth\Model::setProperties
     * @covers \DuoAuth\Model::getProperties
     */
    public function testSetProperties()
    {
        $properties = array(
            'test' => array(
                'type' => 'string'
            )
        );
        $this->model->setProperties($properties);

        $this->assertEquals($properties, $this->model->getProperties());
    }

    /**
     * Test the getter/setter on the configuration handling
     * @covers \DuoAuth\Model::setConfig
     * @covers \DuoAuth\Model::getConfig
     */
    public function testGetSetConfig()
    {
        $config = array('test' => 'setting');

        $this->model->setConfig($config);
        $this->assertEquals($this->model->getConfig(), $config);
    }

    /**
     * Test that an exception is thrown when bad config is given
     * @covers \DuoAuth\Model::setConfig
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConfig()
    {
        $this->model->setConfig('badconfig');
    }

    /**
     * Test that the data is populated when given on construct
     * @covers \DuoAuth\Model::__construct
     */
    public function testSetDataConstruct()
    {
        $data = array('test' => 'foo');
        $model = new \DuoAuth\MockModel($data);

        $this->assertEquals($model->test, 'foo');
    }

    /**
     * Test that an exception is thrown when bad data is given to load
     * @covers \DuoAuth\Model::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidData()
    {
        $model = new \DuoAuth\MockModel();
        $model->load('baddata');
    }

    /**
     * Test that the data can be populated from an object too
     * @covers \DuoAuth\Model::load
     */
    public function testLoadDataFromObject()
    {
        $obj = new \stdClass();
        $obj->test = 'foo';

        $model = new \DuoAuth\MockModel();
        $model->load($obj);

        $this->assertEquals($model->test, 'foo');
    }

    /**
     * Test that setting a property works correctly
     * @covers \DuoAuth\Model::toArray
     * @covers \DuoAuth\Model::__set
     */
    public function testSetProperty()
    {
        $model = new \DuoAuth\MockModel();
        $model->test = 'foo';

        $values = $model->toArray();
        $this->assertEquals($values['test'], 'foo');
    }

    /**
     * Test the getter/setter for integration name
     * @covers \DuoAuth\Model::setIntegration
     * @covers \DuoAuth\Model::getIntegration
     */
    public function testGetSetIntegration()
    {
        $this->model->setIntegration('test');
        $this->assertEquals('test', $this->model->getIntegration());
    }

    /**
     * Test that multiple objects of the type given are populated and returned
     * @covers \DuoAuth\Model::find
     */
    public function testFindMultiple()
    {
        $results = array(
            "response" => array(
                array("test" => "testuser1"),
                array("test" => "testuser2")
            )
        );

        $request = $this->buildMockRequest($results);
        $model = $this->getMock('\DuoAuth\Model', array('getRequest'));

        $model->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $result = $model->find('/test/this','\DuoAuth\MockModel');

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]->test, 'testuser1');
    }

    /**
     * Test that, when a single return value is found, it just assigns the properties
     * @covers \DuoAuth\Model::find
     */
    public function testFindSingle()
    {
        $results = array(
            "response" => array(
                array("test" => "testuser1")
            )
        );

        $request = $this->buildMockRequest($results);
        $model = $this->getMock('\DuoAuth\MockModel', array('getRequest'));

        $model->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $model->find('/test/this','\DuoAuth\MockModel');

        $this->assertEquals($model->test, 'testuser1');
    }

    /**
     * Test that exception is thrown when $type class is invalid
     * @expectedException \InvalidArgumentException
     */
    public function testFindSingleBadClass()
    {
        $results = array(
            "response" => array(
                array("test" => "testuser1"),
                array("test" => "testuser2")
            )
        );

        $request = $this->buildMockRequest($results);
        $model = $this->getMock('\DuoAuth\MockModel', array('getRequest'));

        $model->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $model->find('/test/this','BadClass');
    }

    /**
     * Test that the values are set correctly on a single return value
     * @covers \DuoAuth\Model::find
     */
    public function testReturnSingleInstance()
    {
        $results = array(
            "response" => array("test" => "testuser1")
        );

        $request = $this->buildMockRequest($results);
        $model = $this->getMock('\DuoAuth\MockModel', array('getRequest'));

        $model->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $model->find('/test/this','\DuoAuth\MockModel');

        $this->assertEquals($model->test, 'testuser1');
    }

    /**
     * Test that params are set on request
     * @covers \DuoAuth\Model::find
     */
    public function testFindValidParams()
    {
        $results = array("response" => array());
        $params = array('test' => true);

        $request = $this->buildMockRequest($results);
        $model = $this->getMock('\DuoAuth\MockModel', array('getRequest'));

        $model->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $model->find('/test/this','\DuoAuth\MockModel', $params);
    }

    /**
     * Test the load of valid data
     * @covers \DuoAuth\Model::load
     */
    public function testLoadValidData()
    {
        $model = new \DuoAuth\Model();
        $model->setProperties(array(
            'test1' => array(
                'type' => 'string'
            ),
            'test2' => array(
                'type' => 'string'
            ),
            'test3' => array(
                'type' => 'array',
                'map' => '\DuoAuth\User'
            )
        ));
        $data = array(
            'test1' => 'foo',
            'test2' => 'bar',
            'test3' => array(array('username' => 'testuser1'))
        );
        $model->load($data);
        $result = $model->toArray();

        $this->assertEquals($result['test1'], 'foo');
        $this->assertEquals($result['test2'], 'bar');
        $this->assertEquals($result['test3'][0]->username, 'testuser1');
    }

    /**
     * Test that, when the data has more values than the model's properties
     *     it's not set in the values
     */
    public function testLoadInvalidProperty()
    {
        $model = new \DuoAuth\Model();
        $model->setProperties(array(
            'test1' => array(
                'type' => 'string'
            )
        ));
        $data = array(
            'test1' => 'foo',
            'test2' => 'bar'
        );
        $model->load($data);
        $data = $model->toArray();

        $this->assertEquals(isset($data['test2']), false);
    }
}
