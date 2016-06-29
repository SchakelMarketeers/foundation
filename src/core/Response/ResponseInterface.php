<?php

namespace Schakel\Core\Response;

/**
 * Describes what a response should look like.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
interface ResponseInterface
{
    /**
     * Returns the body, as a string.
     *
     * @return string
     */
    public function getBody();
    /**
     * Returns the HTTP response code, as integer.
     *
     * @return integer
     */
    public function getResponseCode();

    /**
     * Returns any headers set on the renderer.
     *
     * @return string[]
     */
    public function getHeaders();

    /**
     * Returns a string that can be used to identify this response in the logs.
     * For templates this could be the filename, for example.
     *
     * @return string
     */
    public function getDebug();
}
