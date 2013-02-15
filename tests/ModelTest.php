<?php

namespace DuoAuth;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    private $model = null;

    public function setUp()
    {
        $this->model = new \DuoAuth\Model();
    }

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
