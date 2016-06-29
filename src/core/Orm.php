<?php

namespace Schakel\Core;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Schakel\Core\Config;
use Schakel\Core\App;

use Doctrine\DBAL\Logging\EchoSQLLogger;

/**
 * Handles ORM management and getting an EntityManager
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class Orm {

    /**
     * @var EntityManager Single instance of the Doctrine entity manager.
     */
    private static $entityManager = null;

    /**
     * Creates an EntityManager instance, called when needed
     * @return void
     */
    private static function createEntityManager() {
        list($dbParams, $dev) = self::getSettings();

        $xmlMaps = [
            App::getRootPath() . '/lib/orm/model',
            App::getFoundationPath() . '/lib/orm/model'
        ];

        $proxyDir = App::getRootPath() . '/lib/proxies/';

        $config = Setup::createXMLMetadataConfiguration(
            $xmlMaps, $dev, $proxyDir
        );
        self::$entityManager = EntityManager::create($dbParams, $config);

        // if($dev) {
        //     self::$entityManager->getConnection()
        //         ->getConfiguration()
        //         ->setSQLLogger(new EchoSQLLogger());
        // }
    }

    /**
     * @return Doctrine\ORM\EntityManager Instantiated entity manager.
     */
    public static function getEntityManager() {
        if(self::$entityManager === null) {
            self::createEntityManager();
        }
        return self::$entityManager;
    }

    /**
     * Sets the entity manager to something else than the default.
     * Useful if you have your own config and for unit testing.
     *
     * @param Doctrine\ORM\EntityManager $em
     * @return boolean
     */
    public static function setEntityManager(EntityManager $em) {
        if ($em instanceof EntityManager) {
            self::$entityManager = $em;
            return true;
        }

        return false;
    }

    /**
     * Returns the SQL connection settings and debug mode.
     * index 0 {array} SQL settings<br />
     * index 1 {bool} debug mode
     *
     * @return array
     */
    private static function getSettings() {
        $cfg = Config::get('sql');

        $settings = [
            'driver' => $cfg->get('driver', 'pdo_sqlite')
        ];

        if($settings['driver'] === 'pdo_sqlite') {

            $settings['path'] = $cfg->get('path', 'cache/db.sqlite');

            if($settings['path'][0] !== '/') {
                $settings['path'] = App::getRootPath() . '/' . $settings['path'];
            }

        } else {
            $settings['host'] = $cfg->get('hostname', 'localhost');
            $settings['user'] = $cfg->get('username', 'guest');
            $settings['password'] = $cfg->get('password', null);
            $settings['dbname'] = $cfg->get('database', 'autosiero');
        }

        unset($cfg);

        return [$settings, App::getMode() === App::MODE_DEV];
    }

    /**
     * Shorthand method for Orm::getEntityManager()->flush()
     * @return boolean
     */
    public static function flush() {
        return self::getEntityManager()->flush();
    }
}