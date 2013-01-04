<?php

namespace DuoAuth;

class Response
{
    /**
     * Success status for the response
     * @var boolean
     */
    private $success = false;

    /**
     * Body contents for the response
     * @var object
     */
    private $body = null;

    /**
     * Construct the response object
     *     NOTE: If value is set, populates the object with it (setData)
     *
     * @param object Guzzle Response object
     */
    public function __construct($response = null)
    {
        if ($response !== null) {
            $this->setData($response);
        }
    }

    /**
     * Populate the object with data from the response
     *
     * @param object $response Guzzle response object
     */
    public function setData($response)
    {
        if ($response->getStatusCode() == '200') {
            $this->success = true;
        }

        $body = json_decode($response->getBody(true));
        $this->setBody($body->response);
    }

    /**
     * Return the status of the response
     *
     * @return boolean Success/fail of the response
     */
    public function success()
    {
        return ($this->success == true) ? true : false;
    }

    /**
     * Get the body for the current response
     *
     * @return object Response contents
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the body contents for the request
     *
     * @param object Response body contents
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}

?>