<?php

namespace Schakel\Core\Response;

use Schakel\Controller\RouterController;
use Schakel\Core\App;
use Schakel\Core\Config;
use Schakel\Core\TwigExtension;
use Schakel\Model\Wordpress\Page;
use Schakel\Model\Wordpress\Post;

/**
 * Handles Twig template rendering and caching.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @author Joram Schrijver <joram@schakelmarketeers.nl>
 */
class Twig extends AbstractResponse
{
    /**
     * @var String Valid names for templates
     */
    const VALID_NAMES = '/^[a-zA-Z0-9][a-zA-Z0-9\-\_]+(\/[a-zA-Z0-9\-\_]{2,})*\.twig$/';

    /**
     * @var Twig_Environment Instance of Twig Environment
     */
    private $twig;

    private $template = '404.twig';

    private $args = [];

    /**
     * Creates a new Twig environment with filesystem
     */
    public function __construct($template, $responseCode = 200) {
        $dirs = [
            'app' => App::getRootPath() . '/view',
            'base' => App::getFoundationPath() . '/view',
        ];

        // @codeCoverageIgnoreStart
        // Exception state that should not happen
        if(!file_exists($dirs['app']) || !is_dir($dirs['app'])) {
            App::log('alert', "Template directory at {$dirs['app']} is missing!");
            throw new \RuntimeException(
                "Template directory at {$dirs['app']} is missing.",
                E_USER_ERROR
            );
        }
        // @codeCoverageIgnoreEnd

        if($dirs['app'] === $dirs['base']) {
            $fsPaths = $dirs['app'];
        } else {
            $fsPaths = [$dirs['app'], $dirs['base']];
        }

        $loader = new \Twig_Loader_Filesystem($fsPaths);

        $appCfg = Config::get('settings');

        $settings = [];

        // If 'twig-cache' is not set or is not false, use a cache
        if (!isset($appCfg['twig-cache']) || $appCfg['twig-cache'] !== false) {
            $cacheDir = dirname(__DIR__) . '/cache-twig';

            if (!file_exists($cacheDir) && is_writeable(dirname($cacheDir))) {
                mkdir($cacheDir);
            }

            $settings['cache'] = $cacheDir;
        }

        // Debugging is enabled in development mode
        if (App::getMode() == App::MODE_DEV) {
            $settings['debug'] = true;
        }

        App::log('debug', "Created new Twig renderer.", $settings);

        $this->twig = new Twig_Environment($loader, $settings);

        $this->prepareEnvironment();

        // Just one header, since the content type isn't going to change anytime
        // soon.
        $this->addHeader('Content-Type: text/html; charset=utf-8');

        if(!empty($template)) {
            $this->setTemplate($template);
        }

        if(is_int($responseCode)) {
            $this->setResponseCode($responseCode);
        }
    }

    /**
     * Install various extensions and functions into the Twig environment.
     */
    protected function prepareEnvironment() {
        // Debugging is enabled in development mode
        if (App::getMode() == App::MODE_DEV) {
            $this->twig->addExtension(new \Twig_Extension_Debug());
        }

        $this->twig->addExtension(new TwigExtension);
    }

    /**
     * Sets the template for Twig to use.
     *
     * @param string
     * @throws InvalidArgumentException
     * @return Siero\Core\Response\Twig
     */
    public function setTemplate($template) {
        if (!is_string($template) || empty($template)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected $template to be a non-empty string, got %s',
                is_object($template) ? get_class($template) : gettype($template)
            ));
        } elseif (!preg_match(self::VALID_NAMES, $template)) {
            throw new \InvalidArgumentException(sprintf(
                'The $template "%s" is not a valid template name.',
                $template
            ));
        }

        $this->template = $template;
        return $this;
    }

    /**
     * Renders something using Twig
     *
     * @param string $template
     * @param array|null $args
     * @return Siero\Core\Response\Twig
     */
    public function setArgs($args = null) {
        if(!is_array($args)) {
            $args = [];
        }

        // Always extends, in case something else set some vars.
        $this->args = array_merge($this->args, $args);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody() {
        return $this->twig->render($this->template, $this->args);
    }

    /**
     * {@inheritdoc}
     */
    public function getDebug() {
        return sprintf(
            'Twig template %s - %d args',
            $this->template,
            count($this->args)
        );
    }
}
