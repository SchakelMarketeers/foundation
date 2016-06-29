<?php

namespace Schakel\Core\Response;

/**
 * Describes what a renderer should look like and implements basic getters and
 * setters.
 *
 * @author Joram Schrijver <joram@schakelmarketeers.nl>
 */
abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var int
     */
    protected $responseCode = 200;
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Sets the response code for this request.
     *
     * @param int $code
     * @return Siero\Core\Response\AbstractResponse
     * @throws InvalidArgumentException
     */
    public function setResponseCode($code) {
        if (!is_int($code)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected $header to be an integer, got %s',
                is_object($code) ? get_class($code) : gettype($code)
            ));
        }

        if ($code > 599 || $code < 200) {
            throw new \InvalidArgumentException(
                "Response code {$code} is an invalid response code."
            );
        }

        $this->responseCode = $code;
        return $this;
    }

    /**
     * Adds a header for this request. Format must be 'Key: value'.
     *
     * @param string $header
     * @return Siero\Core\Response\AbstractResponse
     * @throws InvalidArgumentException
     */
    public function addHeader($header) {
        if(!is_string($header) || empty($header)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected $header to be a non-empty string, got %s',
                is_object($header) ? get_class($header) : gettype($header)
            ));
        }

        $this->headers[] = $header;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseCode() {
        return $this->responseCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders() {
        return $this->headers;
    }
}
