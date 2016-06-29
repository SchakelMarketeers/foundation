<?php

namespace Schakel\Core\Response;

use Schakel\Core\App;
use Schakel\Core\Config;

/**
 * Handles twig template rendering and caching.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class Json extends AbstractResponse
{
    /**
     * Generate a JSON error response. Call has same structure as
     * Exception::__construct
     * @see \Exception::__construct
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public static function formatError($message = '', $code = 0, \Exception $previous = null) {
        $error = [
            'message' => $message,
            'code' => $code
        ];

        if(App::getMode() === App::MODE_DEV && $previous !== null) {
            $error['previous'] = [
                'type' => get_class($previous),
                'message' => $previous->getMessage(),
                'code' => $previous->getCode(),
                'file' => $previous->getFile(),
                'line' => $previous->getLine()
            ];
        }

        $out = new self($code >= 400 && $code < 599 ? $code : 500);
        $out->setArgs([
            'error' => $error
        ]);
        return $out;
    }

    /**
     * @var array
     */
    private $args = [];

    /**
     * @param int $responseCode
     */
    public function __construct($responseCode = 200) {
        if(is_int($responseCode)) {
            $this->setResponseCode($responseCode);
        }
    }

    /**
     * Renders something using Twig
     *
     * @param string $template
     * @param array|null $args
     * @return Siero\Core\Response\Json
     */
    public function setArgs($args = null) {
        if(!is_array($args)) {
            $args = [];
        }

        // Always extends, in case someting else set some vars.
        $this->args = array_merge($this->args, $args);

        // Return oneself, to be stackable
        return $this;
    }

    /**
     * Returns any additional headers that should be returned, such as
     * content-types.
     *
     * @return string[]
     */
    public function getHeaders() {
        return array_merge($this->headers, [
            'Content-Type: application/json; charset=utf-8'
        ]);
    }

    /**
     * Returns the body, as a string.
     *
     * @return string
     */
    public function getBody() {
        return json_encode($this->args);
    }

    /**
     * {@inheritdoc}
     */
    public function getDebug() {
        return sprintf(
            'Json object with %d node(s)',
            !is_array($this->args) ? 'NULL' : count($this->args)
        );
    }

}