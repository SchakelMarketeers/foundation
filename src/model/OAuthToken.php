<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

use Schakel\Model\User;
use Schakel\Model\Client;

use Schakel\Core\App;

use DateTime;

/**
 * Provides all the basic requirements for an OAuth token.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
abstract class OAuthToken extends Persistable {

    /**
     * @var string
     */
    protected $code;

    /**
     * @var Schakel\Model\Client
     */
    protected $client;

    /**
     * @var Schakel\Model\User
     */
    protected $user;

    /**
     * @var DateTime
     */
    protected $expire;

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * Adds this token to the given user. Needs to be implemented.
     *
     * @param DJO\Model\User
     */
    abstract protected function addToUser(User $user);

    /**
     * Adds this token to the given client. Needs to be implemented.
     *
     * @param DJO\Model\Client
     */
    abstract protected function addToClient(Client $client);

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return Schakel\Model\Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * @return Schakel\Model\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * @return DateTime
     */
    public function getExpire() {
        return $this->expire;
    }

    /**
     * @return int Unix timestamp of expire date
     */
    public function getExpireUnix() {
        return $this->expire ? (int) $this->expire->format('U') : null;
    }

    /**
     * @param string
     */
    public function setCode($code) {

        // Don't allow modifying of token.
        if($this->getId() !== null)
            return false;
        $this->code = $code;
    }

    /**
     * @param Schakel\Model\Client
     */
    public function setClient(Client $client = null) {
        if($client !== null) {
            $this->addToClient($client);
        }

        $this->client = $client;
    }

    /**
     * @param Schakel\Model\User
     */
    public function setUser(User $user = null) {
        if($user !== null) {
            $this->addToUser($user);
        }

        $this->user = $user;
    }

    /**
     * @param DateTime
     */
    public function setExpire(DateTime $expire) {
        $this->expire = $expire;
    }

    /**
     * Sets creation time
     */
    public function beforePersist() {
        $this->created = new DateTime(date('c'));

        return true;
    }

    /**
     * @return boolean
     */
    public function isExpired() {
        return $this->getExpire() < new DateTime('now', App::getTimezone());
    }

}
