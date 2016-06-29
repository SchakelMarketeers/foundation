<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

use Schakel\Model\User;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents an OAuth client
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
abstract class Client extends Persistable {

    const E_NO_NAME = 1;
    const E_NO_REDIRECTURL = 2;
    const E_NO_OWNER = 3;

    /**
     * Generates a random string to use as client secret.
     *
     * @return string
     */
    public static function GenerateSecret() {
        $bytes = random_bytes(16);
        return bin2hex($bytes);
    }

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var Schakel\Model\User
     */
    protected $owner;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection $authorizationTokens
     */
    protected $authorizationTokens;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection $accessTokens
     */
    protected $accessTokens;

    public function __construct() {
        $this->authorizationTokens = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
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
    public function getSecret() {
        return $this->secret;
    }

    /**
     * @return Schakel\Model\User
     */
    public function getOwner() {
        return $this->owner;
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
     * @param string $name
     */
    public function setName($name) {
        if ($name === null) {
            $this->name = null;
            return true;
        } elseif (is_string($name)) {
            $name = trim($name);
            if (strlen($name) > 2 && strlen($name) < 65) {
                $this->name = $name;
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $redirectUrl
     * @return boolean
     */
    public function setRedirectUrl($redirectUrl) {
        if ($redirectUrl === null || (is_string($redirectUrl) && $redirectUrl === '')) {
            $this->redirectUrl = null;
            return true;
        }

        if (!is_string($redirectUrl)) {
            return false;
        }

        $urls = explode(' ', $redirectUrl);
        $finalUrl = [];

        foreach ($urls as $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL))
                continue;

            $urlData = parse_url($url);

            if (!in_array($urlData['scheme'], ['http', 'https']))
                continue;

            if (array_key_exists('user', $urlData))
                continue;

            if (!array_key_exists('path', $urlData) || $urlData['path'] == '/')
                continue;

            $finalUrl[] = $url;
        }

        if (empty($finalUrl)) {
            return false;
        }

        $this->redirectUrl = implode(' ', $finalUrl);
        return true;
    }

    /**
     * @param string $clientSecret
     */
    public function setSecret($secret) {
        if ($secret === null || (is_string($secret) && $secret === '')) {
            $this->secret = (string) $secret;
        } elseif (is_string($secret) && preg_match('/^[a-f0-9]{32}$/i', $secret)) {
            $this->secret = (string) $secret;
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param Schakel\Model\User $user
     */
    public function setOwner(User $owner = null)
    {
        if ($owner !== null) {
            $owner->addClient($this);
        }
        $this->owner = $owner;
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

    /**
     * Makes sure all required keys are set and generates client ID and secret
     * before persisting entry.
     */
    protected function beforePersist() {

        if(empty($this->getName())) {
            throw new \UnexpectedValueException(
                'This Client has no name, which is required.',
                self::E_NO_NAME
            );
        }

        if(empty($this->getRedirectUrl())) {
            throw new \UnexpectedValueException(
                'This Client has no redirect URL, which is required.',
                self::E_NO_REDIRECTURL
            );
        }

        if(empty($this->getOwner())) {
            throw new \UnexpectedValueException(
                'This Client has no owner, which is required.',
                self::E_NO_OWNER
            );
        }

        $this->setSecret(self::GenerateSecret());

        return true;
    }
}
