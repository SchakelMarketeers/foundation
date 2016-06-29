<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

require_once __DIR__ . '/src/bootstrap.php';

use Schakel\Core\Orm;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$entityManager = Orm::getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
