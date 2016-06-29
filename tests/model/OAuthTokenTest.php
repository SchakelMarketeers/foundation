<?php

namespace Schakel\Test\Model;

use DateTime;
use Prophecy\Argument;
use Schakel\Core\Orm;
use Schakel\Model\Client;
use Schakel\Model\OAuthToken;
use Schakel\Model\User;

/**
 * Unit tests for Schakel Marketeers login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @todo testCodeExisting - Needs implementing
 */
class OAuthTokenTest extends PersistableObjectTest {

    /**
     * {@inheritdoc}
     */
    public function getMock($class = OAuthToken::class) {
        return parent::getMock($class);
    }

    /**
     * @covers Schakel\Model\OAuthToken::setCode()
     * @covers Schakel\Model\OAuthToken::getCode()
     */
    public function testCodeNormal() {
        // Get mocks
        $token = $this->getMock();

        $code = 'hello-world-test-token-2902490493';
        $this->assertNull($token->setCode($code));
        $this->assertEquals($code, $token->getCode());
    }

    /**
     * @covers Schakel\Model\OAuthToken::setCode()
     * @covers Schakel\Model\OAuthToken::getCode()
     * @todo Figure out a way to get 'getId' to return non-null.
     */
    public function testCodeExisting() {

        // Get mocks
        $token = $this->getMock(OAT::class);

        if($token->getId() === null) {
            $this->markTestIncomplete(
                'Cannot set ID to non-null, which this test needs.'
            );
            return;
        }

        // Override getId for testing
        $token->expects($this->once())->method('getId');

        $code = 'hello-world-test-token-2902490493';
        $this->assertFalse($token->setCode($code));
    }

    /**
     * @covers Schakel\Model\OAuthToken::setClient()
     * @covers Schakel\Model\OAuthToken::getClient()
     */
    public function testClientNormal() {
        // Get mocks
        $token = $this->getMock();
        $client = $this->getMock(Client::class);

        // Implement addToClient.
        $token  ->expects($this->once())
                ->method('addToClient')
                ->with($this->equalTo($client))
                ->will($this->returnValue(true));

        // Run test
        $token->setClient($client);
        $this->assertEquals($client, $token->getClient());
    }

    /**
     * @covers Schakel\Model\OAuthToken::setClient()
     * @covers Schakel\Model\OAuthToken::getClient()
     */
    public function testClientEmpty() {
        // Get mocks
        $token = $this->getMock();

        // Implement addToClient.
        $token->expects($this->never())->method('addToClient');

        // Run test
        $token->setClient(null);
        $this->assertNull($token->getClient());
    }

    /**
     * @covers Schakel\Model\OAuthToken::setUser()
     * @covers Schakel\Model\OAuthToken::getUser()
     */
    public function testUserNormal() {
        // Get mocks
        $token = $this->getMock();
        $user = $this->getMock(User::class);

        // Implement addToUser.
        $token  ->expects($this->once())
                ->method('addToUser')
                ->with($this->equalTo($user))
                ->will($this->returnValue(true));

        // Run test
        $token->setUser($user);
        $this->assertEquals($user, $token->getUser());
    }

    /**
     * @covers Schakel\Model\OAuthToken::setUser()
     * @covers Schakel\Model\OAuthToken::getUser()
     */
    public function testUserEmpty() {
        // Get mocks
        $token = $this->getMock();

        // Implement addToUser.
        $token->expects($this->never())->method('addToUser');

        // Run test
        $token->setUser(null);
        $this->assertNull($token->getUser());
    }

    /**
     * @covers Schakel\Model\OAuthToken::getCreated()
     * @covers Schakel\Model\OAuthToken::beforePersist()
     */
    public function testCreated() {
        // Override EntityRepository
        $emMock = $this->getMockedEntityManager();

        $emMock->expects($this->once())
            ->method('persist')
            ->will($this->returnValue(null));

        Orm::setEntityManager($emMock);

        // Get mocks
        $token = $this->getMock();

        // Run test
        $this->assertNull($token->getCreated());

        // Persist - faked
        $this->assertTrue($token->persist());
        $persistTime = new DateTime(date('c'));

        // Run 2nd test
        $created = $token->getCreated();
        $this->assertInstanceOf(DateTime::class, $created);
        $this->assertEquals($persistTime, $created);
    }

    /**
     * @covers Schakel\Model\OAuthToken::getExpire()
     * @covers Schakel\Model\OAuthToken::setExpire()
     * @covers Schakel\Model\OAuthToken::isExpired()
     */
    public function testExpire() {
        // Get mocks
        $token = $this->getMock();

        // Set some times
        $expireFutureTime = time() + 60*60*24*14;
        $expirePastTime = time() - 60*15;

        $expireFuture = new DateTime(date('c', $expireFutureTime));
        $expirePast = new DateTime(date('c', $expirePastTime));

        // Run base test
        $this->assertNull($token->getExpire());

        // Set future
        $token->setExpire($expireFuture);
        $this->assertEquals($expireFuture, $token->getExpire());
        $this->assertEquals($expireFutureTime, $token->getExpireUnix());
        $this->assertFalse($token->isExpired());

        // Set past
        $token->setExpire($expirePast);
        $this->assertEquals($expirePast, $token->getExpire());
        $this->assertEquals($expirePastTime, $token->getExpireUnix());
        $this->assertTrue($token->isExpired());
    }

}