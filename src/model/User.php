<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

use Doctrine\Common\Collections\ArrayCollection;

use JsonSerializable;

/**
 * Represents a User
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class User extends Persistable implements JsonSerializable {

    /**
     * @param string $id
     * @return Schakel\Model\LoginRequest|null
     */
    public static function getByGoogleId($id) {
        return self::_findByKeyValue('googleId', (string) $id);
    }

    /**
     * @var string $firstName first name of this user.
     */
    protected $firstName;

    /**
     * @var string $lastName last name of this user.
     */
    protected $lastName;

    /**
     * @var string $email email of this user.
     */
    protected $email;

    /**
     * @var boolean $admin Is this user an admin?
     */
    protected $admin;

    /**
     * @var string $googleId User ID in Google's database
     */
    protected $googleId;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection $clients
     */
    protected $clients;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection $authorizationTokens
     */
    protected $authorizationTokens;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection $accessTokens
     */
    protected $accessTokens;

    public function __construct() {
        $this->clients = new ArrayCollection();
        $this->authorizationTokens = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function getAdmin() {
        return $this->admin;
    }

    /**
     * @return string
     */
    public function getGoogleId() {
        return $this->googleId;
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getClients() {
        return $this->clients;
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getAuthorizationTokens() {
        return $this->authorizationTokens;
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getAccessTokens() {
        return $this->accessTokens;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = (string) $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = (string) $lastName;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = (string) $email;
    }

    /**
     * @param bool $admin
     */
    public function setAdmin($admin) {
        $this->admin = (bool) $admin;
    }

    /**
     * @param string $googleId
     */
    public function setGoogleId($googleId) {
        $this->googleId = (string) $googleId;
    }

    /**
     * @param Schakel\Model\Client $client
     */
    public function addClient(Client $client) {
        $this->clients[] = $client;
    }

    /**
     * @param Schakel\Model\AuthorizationToken $token
     */
    public function addAuthorizationToken(AuthorizationToken $token) {
        $this->authorizationToken[] = $token;
    }

    /**
     * @param Schakel\Model\AccessToken $token
     */
    public function addAccessToken(AccessToken $token) {
        $this->accessToken[] = $token;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'google-id' => $this->getGoogleId(),
            'first-name' => $this->getFirstName(),
            'last-name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'admin' => $this->getAdmin()
        ];
    }


}
