<?php

namespace Schakel\Core\Response;

use Schakel\Core\Config;

/**
 * Handles plain text reponses
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class Plain extends AbstractResponse
{
    /**
     * @var array
     */
    private $args = '';

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
     * @return Siero\Core\Response\Plain
     */
    public function setArgs($args = null) {
        if (is_array($args) || is_object($args)) {
            $args = print_r($args, true);
        } elseif ($args === null) {
            $args = 'null';
        } elseif (is_bool($args)) {
            $args = $args ? 'true' : 'false';
        } else {
            $args = (string) $args;
        }

        $this->args .= $args . PHP_EOL;

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
            'Content-Type: text/plain; charset=utf-8'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBody() {
        return $this->args;
    }

    /**
     * {@inheritdoc}
     */
    public function getDebug() {
        return 'Plain text response of ' . strlen($this->args) . ' bytes';
    }

}