<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

use Schakel\Core\Orm;
use PDOException;

/**
 * Represents a persistable object, which has some methods to help searching,
 * getting entities and saving / updating entities.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
abstract class Persistable {

    /**
     * Returns all stored objects.
     *
     * @return Schakel\Model\Persistable[]
     */
    public static function getAll() {
        $repo = Orm::getEntityManager()->getRepository(static::class);
        return $repo->findAll();
    }

    /**
     * Returns an object with the given id
     *
     * @param mixed $id
     * @return \Schakel\Model\Persitable|null
     */
    public static function getById($id) {
        $repo = Orm::getEntityManager()->getRepository(static::class);
        return $repo->find($id);
    }

    /**
     * Returns an object for the given value
     *
     * @param string $key
     * @param mixed $value
     * @return \Schakel\Model\Persitable|null
     */
    protected static function _findByKeyValue($key, $value) {
        $repo = Orm::getEntityManager()->getRepository(static::class);
        return $repo->findOneBy([
            (string) $key => $value
        ]);
    }

    /**
     * Returns all objects for the given value
     *
     * @param string $key
     * @param mixed $value
     * @param int $limit
     * @return \Schakel\Model\Persitable|null
     */
    protected static function _findAllByKeyValue($key, $value, $limit = null) {
        $repo = Orm::getEntityManager()->getRepository(static::class);
        return $repo->findBy([
            (string) $key => $value
        ], null, $limit);
    }

    /**
     * @param int
     */
    protected $id;

    /**
     * Returns the ID of this object, or NULL if the object has not been
     * persisted yet.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Hook called just before the object is persisted. Return false to
     * cancel persisting.
     *
     * @return bool
     */
    protected function beforePersist() {
        return true;
    }

    /**
     * Adds this object to the database. Use \Schakel\Core\Orm::flush() to
     * save changes to exising entities.
     * @return boolean
     */
    public function persist() {
        $em = Orm::getEntityManager();

        if($this->getId() === null) {

            if(!$this->beforePersist()) {
                return false;
            }

            try {
                $em->persist($this);
                return true;
            } catch(PDOException $e) {
                error_log(sprintf(
                    'Unexpected PDO exception: %s (%d)',
                    $e->getMessage(),
                    $e->getCode()
                ));
                return false;
            }
        }
    }
}
