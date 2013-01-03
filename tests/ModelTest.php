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
}
