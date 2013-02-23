<?php

namespace DuoAuth;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \DuoAuth\Error::clear();
    }

    /**
     * Test that the setter and getter work for a single value
     * @covers \DuoAuth\Error::add
     * @covers \DuoAuth\Error::get
     */
    public function testGetSetError()
    {
        \DuoAuth\Error::add('foo', 'test');
        $this->assertEquals('foo', \DuoAuth\Error::get('test'));
    }

    /**
     * Test that the correct value is returned when no key was provided
     * @covers \DuoAuth\Error::add
     * @covers \DuoAuth\Error::get
     */
    public function testGetSetErrorNoKey()
    {
        \DuoAuth\Error::add('foo');
        $this->assertEquals('foo', \DuoAuth\Error::get(0));   
    }

    /**
     * Test that the setting and getting of all errors works
     * @covers \DuoAuth\Error::add
     * @covers \DuoAuth\Error::get
     */
    public function testGetSetErrorAll()
    {
        \DuoAuth\Error::add('foo', 'test');
        $all = \DuoAuth\Error::get();

        $this->assertEquals(array('test' => 'foo'), $all);
    }

    /**
     * Test that setting and removing one error, by key works correctly
     * @covers \DuoAuth\Error::add
     * @covers \DuoAuth\Error::remove
     * @covers \DuoAuth\Error::get
     */
    public function testSetRemoveError()
    {
        \DuoAuth\Error::add('foo1', 'test1');
        \DuoAuth\Error::add('foo2', 'test2');

        \DuoAuth\Error::remove('test1');
        $all = \DuoAuth\Error::get();

        $this->assertEquals(
            array('test2' => 'foo2'),
            $all
        );
    }

    /**
     * Test that the removal of an invalid key returns false
     * @covers \DuoAuth\Error::add
     * @covers \DuoAuth\Error::remove
     */
    public function testSetRemoveInvalidKey()
    {
        \DuoAuth\Error::add('foo1', 'test1');
        $return = \DuoAuth\Error::remove('badkey');

        $this->assertFalse($return);
    }

    /**
     * Test that the clearing of errors works correctly
     * @covers \DuoAuth\Error::add
     * @covers \DuoAuth\Error::clear
     */
    public function testClearErrors()
    {
        \DuoAuth\Error::add('foo1', 'test1');
        $return = \DuoAuth\Error::clear();
        $all = \DuoAuth\Error::get();

        $this->assertEmpty($all);
        $this->assertTrue($return);
    }
}