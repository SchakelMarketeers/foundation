<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

use Doctrine\Common\Collections\ArrayCollection;

use Schakel\Model\Client;

use DateTime;
use DateInterval;
use DateTimeImmutable;

/**
 * Represents a User
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class LoginRequest extends Persistable {

    const EXPIRE_INTERVAL = 'PT10M';

    /**
     * @param string $state
     * @return Schakel\Model\LoginRequest|null
     */
    public static function getByState($state) {
        return self::_findByKeyValue('state', (string) $state);
    }

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var \DateTime
     */
    protected $expire;

    /**
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getRedirectUrl() {
        return $this->redirectUrl;
    }

    /**
     * @return string
     */
    public function getSessionId() {
        return $this->sessionId;
    }

    /**
     * @return \DateTime
     */
    public function getExpire() {
        return $this->expire;
    }

    /**
     * @param string $state
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl) {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Sets expiration time and session ID on creation
     *
      * @return boolean
     */
    public function beforePersist() {
        $expire = new DateTime();
        $expire->add(new DateInterval(self::EXPIRE_INTERVAL));

        $this->sessionId = session_id();
        $this->expire = $expire;

        return true;
    }

    /**
     * @return boolean
     */
    public function isExpired() {
        $_ex = $this->getExpire();

        if(!$_ex)
            return false;

        return ((int) $_ex->format('U')) < time();
    }

    /**
     * @return boolean
     */
    public function isOwner() {
        return $this->sessionId == session_id();
    }


}
