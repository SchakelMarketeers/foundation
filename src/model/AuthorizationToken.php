<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

/**
 * Represents an OAuth authorization token
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class AuthorizationToken extends OAuthToken {

    const TOKEN_LIFETIME = 'PT5M';

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @return string
     */
    public function getRedirectUrl() {
        return $this->redirectUrl;
    }

    /**
     * @param string
     */
    public function setRedirectUrl($redirectUrl) {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Adds this token to the given user. Needs to be implemented.
     *
     * @param DJO\Model\User
     */
     protected function addToUser(User $user) {
        $user->addAuthorizationToken($this);
    }

    /**
     * Adds this token to the given client. Needs to be implemented.
     *
     * @param DJO\Model\Client
     */
     protected function addToClient(Client $client) {
        $client->addAuthorizationToken($this);
    }
}
