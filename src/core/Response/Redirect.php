<?php

namespace Schakel\Core\Response;

/**
 * Handles redirects.
 *
 * @author Joram Schrijver <joram@schakelmarketeers.nl>
 */
class Redirect extends AbstractResponse
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     * @param int $responseCode
     */
    public function __construct($url, $responseCode = 303) {
        if(is_int($responseCode)) {
            $this->setResponseCode($responseCode);
        }

        $this->url = $url;

        $this->addHeader("Location: {$url}");
    }

    /**
     * Returns the body, as a string.
     *
     * @return string
     */
    public function getBody() {
        return sprintf(
            '<a href="%s">Continue to %s</a>',
            str_replace('"', '\"', $this->url),
            htmlspecialchars($this->url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDebug() {
        return "Redirect to {$this->url}";
    }
}
