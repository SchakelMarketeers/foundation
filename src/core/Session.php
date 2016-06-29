<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Core;

use Schakel\Model\User;
use Schakel\Controller\RouterController;

/**
 * Handles indicators of the session
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class Session {

    const ID_KEY = 'login-userid';
    const ID_TIMEOUT = 'login-exp';

    const LOGIN_LIFETIME = 'PT3H';

    /**
     * @var Schakel\Model\User
     */
    private static $cachedUser;

    /**
     * @return boolean
     */
    public static function loggedIn() {
        return (
            isset($_SESSION[self::ID_KEY]) &&
            isset($_SESSION[self::ID_TIMEOUT]) &&
            $_SESSION[self::ID_TIMEOUT] >= new \DateTime
        );
    }

    /**
     * @return int|null
     */
    public static function getUserId() {
        return self::loggedIn() ? $_SESSION[self::ID_KEY] : null;
    }

    /**
     * @return Schakel\Model\User|null
     */
    public static function getUser() {

        if(!self::loggedIn()) {
            return null;
        }

        if(self::$cachedUser == null) {
            self::$cachedUser = User::getById(self::getUserId());

            if(self::$cachedUser == null) {
                self::logout();
                return null;
            }
        }

        return self::$cachedUser;
    }

    /**
     * @param Schakel\Model\User $user
     * @return boolean
     */
    public static function login(User $user) {
        if($user == null) {
            return false;
        }

        $time = new \DateTime(date('c'));
        $time->add(new \DateInterval(self::LOGIN_LIFETIME));

        // Make it immutable
        $timeout = \DateTimeImmutable::createFromMutable($time);

        $_SESSION[self::ID_KEY] = $user->getId();
        $_SESSION[self::ID_TIMEOUT] = $timeout;

        self::$cachedUser = $user;

        return true;
    }

    /**
     * @return boolean
     */
    public static function logout() {
        unset($_SESSION[self::ID_KEY]);
        unset($_SESSION[self::ID_TIMEOUT]);
        self::$cachedUser = null;
        return true;
    }

    public static function buildUrl($url, $data) {
        $requestData = http_build_query($data);

        if(is_array($url)) {
            $url = call_user_func_array([$router, 'generate'], $url);
        } elseif(substr($url, 0, 1) !== '/') {
            $url = RouterController::getInstance()->generate($url);
        }

        if(strpos($url, '?') !== false)
            return $url . '&' . $requestData;
        else
            return $url . '?' . $requestData;
    }

    /**
     * Returns next path, used so this script is pretty DRY.
     *
     * @return string
     */
    private static function getNext() {
        if(isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']))
            return $_SERVER['REQUEST_URI'];
        return '';
    }

    /**
     * @return \Schakel\Core\Redirect|boolean
     */
    public static function requiresLogin() {
        if(!self::loggedIn()) {

            $pageSource = filter_input(INPUT_GET, 'req_source');
            if($pageSource === 'logout') {
                $out = new Twig('relogin.twig');
                $out->setArgs([
                    'login' => self::getLoginUrl()
                ]);
                return $out;
            } else {
                return new Redirect(self::getLoginUrl());
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public static function getLoginUrl() {
        return self::buildUrl('oauth-login', ['next' => self::getNext()]);
    }

    /**
     * @return string
     */
    public static function getLogoutUrl() {
        return self::buildUrl('logout', ['next' => self::getNext()]);
    }

    /**
     * The session object can be instantiated, for stuff such as twig. But since
     * we only have static methods, we need to pipe all calls.
     */
    public function __call($method, $args) {
        if(method_exists(self::class, $method)) {
            return call_user_func_array([self::class, $method], $args);
        }
    }
}