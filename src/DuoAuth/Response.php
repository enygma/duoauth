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
     * Get the current "success" status
     * @return boolean Success status
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set the success status
     * @param boolean $status Success status
     */
    public function setSuccess($status)
    {
        if (!is_bool($status)) {
            throw new \InvalidArgumentException('Success status must be boolean');
        }
        $this->success = $status;
    }

    /**
     * Populate the object with data from the response
     *
     * @param object $response Guzzle response object
     */
    public function setData($response)
    {
        if ($response->getStatusCode() == '200') {
            $this->setSuccess(true);
        }

        $body = json_decode($response->getBody(true));
        if (isset($body->response)) {
            $this->setBody($body->response);
        } else {
            $this->setBody($body);

            // check for a "stat" of "FAIL"
            $body = $this->getBody();
            if (isset($body->stat) && $body->stat == 'FAIL') {
                \DuoAuth\Error::add($body->message);
                $this->setSuccess(false);
            }
        }
    }

    /**
     * Return the status of the response
     *
     * @return boolean Success/fail of the response
     */
    public function success()
    {
        return ($this->getSuccess() == true) ? true : false;
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