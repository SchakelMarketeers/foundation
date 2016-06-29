<?php

namespace Schakel\Core;

/**
 * Describes what a renderer should look like and implements basic getters and
 * setters.
 *
 * @author Joram Schrijver <joram@schakelmarketeers.nl>
 */
abstract class AbstractRenderer {
    protected $responseCode = 303;
    protected $headers = [];

    /**
     * Returns the body, as a string.
     *
     * @return string
     */
    abstract public function getBody();

    /**
     * Sets the response code for this request.
     *
     * @param int $code
     */
    public function setResponseCode($code) {
        if (is_int($code)) {
            $this->responseCode = $code;
            return true;
        }

        return false;
    }

    /**
     * Adds a header for this request. Format must be 'Key: value'.
     *
     * @param string $header
     */
    public function addHeader($header) {
        if(!is_string($header) || empty($header)) {
            return false;
        }

        $this->headers[] = $header;
        return true;
    }

    /**
     * Returns the HTTP response code, as integer.
     *
     * @return integer
     */
    public function getResponseCode() {
        if($this->responseCode > 510 || $this->responseCode < 200) {
            return 500;
        } else {
            return $this->responseCode;
        }
    }

    /**
     * Returns any headers set on the renderer.
     *
     * @return string[]
     */
    public function getHeaders() {
        return $this->headers;
    }
}
