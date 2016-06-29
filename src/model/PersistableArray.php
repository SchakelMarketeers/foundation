<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Model;

use ArrayAccess;
use Countable;

/**
 * Represents a persistable object, which has some methods to help searching,
 * getting entities and saving / updating entities.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
abstract class PersistableArray
    extends Persistable
    implements ArrayAccess, Countable {

    /**
     * Expects an array containing 2 arrays on key get and set.
     *
     * Example: ['set' => ['id' => 'setId'], 'get' => ['id' => 'getId']]
     *
     * @return array[]
     */
    abstract protected function getArrayMapping();

    /**
     * Safety wrapper for getArrayMapping, to make sure the response meets
     * the requirements
     */
    private function _getArrayMapping() {
        $data = $this->getArrayMapping();

        $out = [
            'get' => [],
            'set' => []
        ];

        if(isset($data['get']) && is_array($data['get'])) {
            $out['get'] = $data['get'];
        }

        if(isset($data['set']) && is_array($data['set'])) {
            $out['set'] = $data['set'];
        }

        return $out;
    }


    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        $_map = $this->_getArrayMapping();

        return array_key_exists($offset, $_map['get']);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        $_map = $this->_getArrayMapping();

        if(array_key_exists($offset, $_map['get'])) {
            return call_user_func([$this, $_map['get'][$offset]]);
        } else {
            return null;
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return mixed
     */
    public function offsetSet($offset, $value) {
        $_map = $this->_getArrayMapping();

        if(array_key_exists($offset, $_map['set'])) {
            return call_user_func([$this, $_map['set'][$offset]], $value);
        } else {
            return false;
        }

    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetUnset($offset) {
        $this->offsetSet($offset, null);
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->_getArrayMapping()['get']);
    }

}
