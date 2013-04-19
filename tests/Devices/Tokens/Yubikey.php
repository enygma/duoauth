<?php

namespace DuoAuth;

class YubikeyTokenTest extends BaseModelHelper
{
    /**
     * Test that exception is thrown when required field is missing
     * @covers \DuoAuth\Devices\Tokens\Yubikey::save
     * @expectedException \InvalidArgumentException
     */
    public function testSaveRequiredFields()
    {
        $token = new \DuoAuth\Devices\Tokens\Yubikey();
        $token->save();
    }
}

