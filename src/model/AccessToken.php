<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

/**
 * Represents an OAuth access token
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class AccessToken extends OAuthToken {

    const TOKEN_LIFETIME = 'PT1H';

    /**
     * Adds this token to the given user. Needs to be implemented.
     *
     * @param DJO\Model\User
     */
     protected function addToUser(User $user) {
        $user->addAccessToken($this);
    }

    /**
     * Adds this token to the given client. Needs to be implemented.
     *
     * @param DJO\Model\Client
     */
     protected function addToClient(Client $client) {
        $client->addAccessToken($this);
    }
}
