<?php

namespace DuoAuth;

class Response
{
    private $success = false;
    private $body = null;

    public function __construct($response = null)
    {
        if ($response !== null) {
            $this->setData($response);
        }
    }

    public function setData($response)
    {
        if ($response->getStatusCode() == '200') {
            $this->success = true;
        }

        $body = json_decode($response->getBody(true));
        $this->body = $body->response;
    }

    public function success()
    {
        return ($this->success == true) ? true : false;
    }

    public function getBody()
    {
        return $this->body;
    }
}

?>