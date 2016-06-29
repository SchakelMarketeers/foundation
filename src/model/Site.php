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
class Site extends Client {

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $updateCount;

    /**
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getUpdateCount() {
        return $this->updateCount;
    }

    /**
     * @param string $version
     */
    public function setVersion($version) {
        $this->version = (string) $version;
    }

    /**
     * @param string $version
     */
    public function setUpdateCount($updateCount) {
        $this->updateCount = (string) $updateCount;
    }

}
