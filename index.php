<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

use Schakel\Controller\ErrorController;
use Schakel\Controller\RouterController;
use Schakel\Core\App;

require_once __DIR__ . '/src/bootstrap.php';

$router = RouterController::getInstance();

// Register app ids
$router->addMatchTypes([
    'uuid' => '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[1-5][0-9A-Fa-f]{3}-[89ABab][0-9A-Fa-f]{3}-[0-9A-Fa-f]{12}'
]);

// Route definitions
$router->map(
    'GET', '/',
    ['HomeController', 'renderHome'],
    'home'
);

// App list
$router->map(
    'GET', '/apps',
    ['AppController', 'listApps'],
    'app-list'
);
$router->map(
    'GET|POST', '/apps/add',
    ['AppController', 'addApp'],
    'app-add'
);
$router->map(
    'GET', '/apps/[uuid:id]',
    ['AppController', 'infoApp'],
    'app-info'
);
$router->map(
    'GET|POST', '/apps/[uuid:id]/edit',
    ['AppController', 'editApp'],
    'app-edit'
);
$router->map(
    'GET|POST', '/apps/[uuid:id]/delete',
    ['AppController', 'deleteApp'],
    'app-delete'
);

// Site list
$router->map(
    'GET', '/sites',
    ['SiteController', 'listSites'],
    'site-list'
);
$router->map(
    'GET|POST', '/site/add',
    ['SiteController', 'addSite'],
    'site-add'
);
$router->map(
    'GET', '/sites/[uuid:id]',
    ['SiteController', 'infoSite'],
    'site-info'
);
$router->map(
    'GET|POST', '/sites/[uuid:id]/edit',
    ['SiteController', 'editSite'],
    'site-edit'
);
$router->map(
    'GET|POST', '/sites/[uuid:id]/delete',
    ['SiteController', 'deleteSite'],
    'site-delete'
);

// User list
$router->map(
    'GET', '/users',
    ['UserController', 'listUsers'],
    'user-list'
);
$router->map(
    'GET|POST', '/users/add',
    ['UserController', 'addUser'],
    'user-add'
);
$router->map(
    'GET', '/users/[i:id]',
    ['UserController', 'infoUser'],
    'user-info'
);

// OAuth server
$router->map(
    'GET|POST', '/oauth/token',
    ['AuthServerController', 'renderToken'],
    'oauth-token'
);
$router->map(
    'GET', '/oauth/auth',
    ['AuthServerController', 'renderAuthorize'],
    'oauth-auth'
);
$router->map(
    'GET', '/oauth/api/[:act]?',
    ['AuthServerController', 'renderApi'],
    'oauth-api'
);

// OAuth client
$router->map(
    'GET', '/oauth/login',
    ['AuthLoginController', 'loginDo'],
    'oauth-login'
);

$router->map(
    'GET', '/oauth/return',
    ['AuthLoginController', 'loginBack'],
    'oauth-return'
);

// Session control
$router->map(
    'GET', '/logout',
    ['AuthLoginController', 'logoutDo'],
    'logout'
);

// Cronjob
$router->map(
    'GET', '/util/cron',
    ['CronController', 'runCron']
);

$match = $router->match();


$controller = null;
$method = null;

$errorController = new ErrorController();

if($match) {
    $controllerName = "Schakel\\Controller\\{$match['target'][0]}";
    $methodName = $match['target'][1];
    if(class_exists($controllerName)) {
        $controller = new $controllerName();
        if(method_exists($controller, $methodName)) {
            $method = $methodName;
        }
    }
}

if(!$method) {
    $controller = $errorController;
    $method = 'renderError';
}

$args = [];
if(isset($match['params']) && is_array($match['params'])) {
    $args = array_merge($args, $match['params']);
}

if(isset($match['name'])) {
    $args['_name'] = $match['name'];
}

// Try to call the method, but sandbox the call in case anything goes wrong.
try {
    $response = call_user_func([$controller, $method], $args);
} catch(Exception $e) {

    // Forward to appropriate response handler.
    $response = $errorController->renderError($e);

    // Always log uncaught exceptions!
    error_log(sprintf(
        'Uncaught %s exception: %s (%d)',
        get_class($e),
        $e->getMessage(),
        $e->getCode()
    ));
}

$out = '';

try {
    // Try to get the body. Programs like Twig can throw exceptions here.
    $out = $response->getBody();
} catch(Exception $e) {

    // Forward to appropriate response handler.
    $response = $errorController->renderError($e);

    // Re-sandbox the getBody, since there may be a tougher exception in stuff
    // like twig.
    $recoverable = true;
    try {
        $out = $response->getBody();
    } catch(Exception $e) {

        // Flag as non-recoverable
        $recoverable = false;

        // Return appropriate data to user.
        http_response_code(500);
        echo file_get_contents(__DIR__ . '/src/view/error/crit-error.html');
    } finally {
        // Always log uncaught exceptions!
        error_log(sprintf(
            '%s %s exception: %s (%d)',
            $recoverable ? 'Uncaught' : 'Breaking uncaught',
            get_class($e),
            $e->getMessage(),
            $e->getCode()
        ));

        if(!$recoverable) {
            exit();
        }
    }
}


// Get headers and response code

http_response_code($response->getResponseCode());

$headers = $response->getHeaders();
foreach($headers as $hdr) {
    header($hdr);
}

// Check if the client said they understand gzip
$acceptEncoding = filter_input(\INPUT_SERVER, "HTTP_ACCEPT_ENCODING");
if(!empty($acceptEncoding)) {
    $canCompress = preg_match("/(?:;\s*|^)gzip(?:[;,]|$)/i", $acceptEncoding);
} else {
    $canCompress = false;
}

// Attempt to gzip content.
if($canCompress && App::getMode() !== App::MODE_DEV) {
    $out = gzencode($out, 7);
    header('Content-Encoding: gzip');
    header(sprintf('Content-Length: %d', strlen($out)));
}

// Output, we're done here.
echo $out;
