<?php

namespace Schakel\Controller;

use Schakel\Core\App;
use Schakel\Core\Response\Redirect;

use AltoRouter;

/**
 * Makes sure only one Router exists, so we don't have to result to using
 * $router globals.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @author Joram Schrijver <joram@schakelmarketeers.nl>
 */
class RouterController extends AltoRouter
{
    /**
     * Returns a single instance, which is then re-used for the duration of the
     * request.
     *
     * @return Siero\Controller\RouterController
     */
    public static function getInstance() {
        static $inst;

        if (!$inst) {
            $inst = new self;
        }

        return $inst;
    }

    /**
     * @var array
     */
    protected $matches = [];

    /**
     * Toggle the trailing slash on a URL, taking care to properly handle the
     * query string that could come after it.
     *
     * @param string $requestUrl The URL to toggle the trailing slash on. Can
     *                           also be omitted or null, in which case
     *                           $_SERVER['REQUEST_URI'] is used.
     */
    public function switchTrailingSlash($requestUrl = null) {
        if($requestUrl === null) {
            $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        }

        $queryString = '';
        if (($strpos = strpos($requestUrl, '?')) !== false) {
            list($requestUrl, $queryString) = explode('?', $requestUrl);
        }
        if (!empty($queryString)) {
            $queryString = '?' . $queryString;
        }

        $len = strlen($requestUrl);
        if ($len > 0 && $requestUrl[$len - 1] === '/') {
            $requestUrl = substr($requestUrl, 0, $len - 1);
        } elseif ($len > 0 && $requestUrl[$len - 1] !== '/') {
            $requestUrl = $requestUrl . '/';
        }

        return $requestUrl . $queryString;
    }

    /**
     * {@inheritdoc}
     */
    public function match($requestUrl = null, $requestMethod = null) {
        $name = sprintf('%s-%s', $requestUrl, $requestMethod);

        // If we have a cached result, return it.
        if(array_key_exists($name, $this->matches)) {
            return $this->matches[$name];
        }

        $match = parent::match($requestUrl, $requestMethod);

        if($match === false) {
            return $this->matches[$name] = $match;
        }

        // Params remain URLencoded, decode them
        if(array_key_exists('params', $match) && is_array($match['params'])) {
            $match['params'] = array_map(function ($param) {
                return urldecode($param);
            }, $match['params']);
        }

        // Cache and return
        return $this->matches[$name] = $match;
    }

    /**
     * Create a Redirect if the current URL doesn't match the canonical one, as
     * generated using generate.
     *
     * @param string $routeName The name of the route to generate a URL for.
     * @param array $arguments The arguments to fill the route template with.
     *                         The values will be urlencoded.
     * @param array $queryArguments An optional array of query arguments to
     *                              build a query string from when creating a
     *                              redirect.
     * @return Redirect|null
     */
    public function maybeRedirect($routeName, $arguments, $queryArguments = null) {
        // Arguments are urldecoded during the matching process, so we need to
        // urlencode them here again.
        $urlencodedArguments = [];
        foreach ($arguments as $key => $value) {
            $urlencodedArguments[$key] = urlencode($value);
        }
        $canonical = $this->generate($routeName, $urlencodedArguments);

        if ($queryArguments === null) {
            if (isset($_SERVER['QUERY_STRING'])) {
                $queryString = $_SERVER['QUERY_STRING'];
            } else {
                $queryString = '';
            }
        } else {
            $queryString = http_build_query($queryArguments);
        }
        if (!empty($queryString)) {
            $queryString = '?' . $queryString;
        }

        App::log('debug', 'Redirecting non-canonical {a} to {b}', [
            'a' => $_SERVER['REQUEST_URI'],
            'b' => $canonical  . $queryString
        ]);

        if ($_SERVER['REQUEST_URI'] !== ($canonical . $queryString)) {
            return new Redirect($canonical . $queryString);
        }

        return null;
    }

    /**
     * Reversed routing
     *
     * Generate the URL for either a named route or a particular model.
     *
     * @param string $object The route or model.
     * @param array $params Associative array of parameters to replace
     *                      placeholders with, if $object is a route name.
     * @return string The generated URL
     * @throws Exception
     */
    public function generate($object, array $params = []) {
        if (is_string($object)) {
            return parent::generate($object, $params);
        } else {
            return null;
        }
    }
}
