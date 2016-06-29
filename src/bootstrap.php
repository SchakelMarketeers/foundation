<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

use Schakel\Core\App;

$ComposerDir = __DIR__ . '/vendor/autoload.php';

define('IN_CLI', php_sapi_name() === 'cli');

// Check for Composer
if(!file_exists($ComposerDir)) {
    error_log('Website does not contain dependancies in vendor dir.');

    if(IN_CLI) {
        exit(1);
    }

    http_response_code(500);
    echo file_get_contents(__DIR__ . '/view/error/dep-error.html');
    exit();
}

if(version_compare(PHP_VERSION, '5.5', '<')) {
    error_log('Server is running an outdated PHP.');

    if(IN_CLI) {
        exit(1);
    }

    http_response_code(500);
    echo file_get_contents(__DIR__ . '/view/error/dep-error.html');
    exit();
}

// Load composer
require_once $ComposerDir;

date_default_timezone_set(App::Timezone);

// Load correct opmode
if (IN_CLI) {
    App::setOpMode(App::OPMODE_CLI);
} else {
    $userAgent = filter_input(INPUT_SERVER, 'USER_AGENT');
    $requestUrl = filter_input(INPUT_SERVER, 'REQUEST_URI');

    if(!$userAgent && isset($_SERVER['USER_AGENT']))
        $userAgent = $_SERVER['USER_AGENT'];

    if(!$requestUrl && isset($_SERVER['REQUEST_URI']))
        $requestUrl = $_SERVER['REQUEST_URI'];

    // Detect API calls
    if(preg_match('/^\/api\//', $requestUrl))
        App::setOpMode(App::OPMODE_API);
    // Auto-detect Guzzle, since we use it for OAuth2
    elseif(strpos($userAgent, 'GuzzleHttp/') !== false)
        App::setOpMode(App::OPMODE_API);
    else
        App::setOpMode(App::OPMODE_WEB);
}

// Set cookie param and name
$cookie = session_get_cookie_params();
$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];

session_name('schakel_login');
session_set_cookie_params(
    $cookie['lifetime'], '/',
    $cookie['domain'], $isHttps,
    true
);
session_start();

// Done.
