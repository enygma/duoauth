<?php

namespace DuoAuth;

class MockResponse
{
    private $statusCode = '200';
    private $body = null;

    public function setBody($body)
    {
        $this->body = $body;
    }
    public function getBody()
    {
        return $this->body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
