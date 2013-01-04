<?php

namespace DuoAuth;

class MockClient
{
    public function __call($method, $args)
    {
        echo 'mocked: '.$method."\n";
    }
}