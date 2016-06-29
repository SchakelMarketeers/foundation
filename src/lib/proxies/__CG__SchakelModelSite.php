<?php

namespace DoctrineProxies\__CG__\Schakel\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Site extends \Schakel\Model\Site implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'version', 'updateCount', 'name', 'redirectUrl', 'secret', 'owner', 'authorizationTokens', 'accessTokens', 'id'];
        }

        return ['__isInitialized__', 'version', 'updateCount', 'name', 'redirectUrl', 'secret', 'owner', 'authorizationTokens', 'accessTokens', 'id'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Site $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getVersion', []);

        return parent::getVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdateCount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdateCount', []);

        return parent::getUpdateCount();
    }

    /**
     * {@inheritDoc}
     */
    public function setVersion($version)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVersion', [$version]);

        return parent::setVersion($version);
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdateCount($updateCount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdateCount', [$updateCount]);

        return parent::setUpdateCount($updateCount);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', []);

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectUrl()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRedirectUrl', []);

        return parent::getRedirectUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function getSecret()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSecret', []);

        return parent::getSecret();
    }

    /**
     * {@inheritDoc}
     */
    public function getOwner()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOwner', []);

        return parent::getOwner();
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationTokens()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAuthorizationTokens', []);

        return parent::getAuthorizationTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessTokens()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAccessTokens', []);

        return parent::getAccessTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectUrl($redirectUrl)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRedirectUrl', [$redirectUrl]);

        return parent::setRedirectUrl($redirectUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function setSecret($secret)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSecret', [$secret]);

        return parent::setSecret($secret);
    }

    /**
     * {@inheritDoc}
     */
    public function setOwner(\Schakel\Model\User $owner)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOwner', [$owner]);

        return parent::setOwner($owner);
    }

    /**
     * {@inheritDoc}
     */
    public function addAuthorizationToken(\Schakel\Model\AuthorizationToken $token)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addAuthorizationToken', [$token]);

        return parent::addAuthorizationToken($token);
    }

    /**
     * {@inheritDoc}
     */
    public function addAccessToken(\Schakel\Model\AccessToken $token)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addAccessToken', [$token]);

        return parent::addAccessToken($token);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function persist()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'persist', []);

        return parent::persist();
    }

}