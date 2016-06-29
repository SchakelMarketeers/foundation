<?php

namespace Schakel\Core;

use Noodlehaus\Config as NoodlehausConfig;
use Noodlehaus\Exception\FileNotFoundException;

use Schakel\Core\App;

/**
 * Creates a Config object based on the Noodlehaus system.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
final class Config
{
    /**
     * @var String sprintf string to use to construct the path to config files.
     * First string should be path of system root, 2nd string should be
     * filename.
     */
    const ConfigBaseFile = '%s/%s.example.json';

    /**
     * @var String sprintf string to use to construct the path to config files.
     * First string should be path of system root, 2nd string should be
     * filename.
     */
    const ConfigUserFile = '?%s/%s.json';

    /**
     * Returns true if this name is valid, false otherwise
     *
     * @param string $name
     * @return boolean
     */
    public static function isValidName($name) {
        return is_string($name) && preg_match('/^[a-z0-9\-\_]+$/', $name);
    }

    /**
     * @param string filename
     * @return array
     */
    protected static function getPaths($config) {
        static $cachedLinks = [];

        if (!is_string($config)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected config name to be a string, got %s.',
                gettype($config)
            ));
        }

        if (isset($cachedLinks[$config])) {
            return $cachedLinks[$config];
        }

        $appCfg = App::getRootPath() . '/../config';
        $fndCfg = App::getFoundationPath() . '/../config';

        $check = [];

        if ($fndCfg !== $appCfg) {
            $file = sprintf(self::ConfigBaseFile, $fndCfg, $config);
            if(file_exists($file)) {
                $check[] = $file;
            }
        }

        $file = sprintf(self::ConfigBaseFile, $appCfg, $config);
        if (file_exists($file)) {
            $check[] = $file;
        }

        if (empty($check)) {
            throw new \RuntimeException(
                "No config with name {$config} was found. " .
                'Are you sure the default files exist?'
            );
        }

        $check[] = sprintf(self::ConfigUserFile, $appCfg, $config);

        $cachedLinks[$config] = $check;

        return $cachedLinks[$config];
    }

    /**
     * Returns the config. Returns NULL if the given name is not valid (only
     * a-z, 0-9, dashes and underscores are allowed). Throws exception if base
     * config doesn't exist.
     *
     * @param string $name
     * @return \Noddlehaus\Config|null
     */
    public static function get($name) {

        if (!self::isValidName($name)) {
            return null;
        }

        $paths = self::getPaths($name);

        try {
            // Construct the config using a base and user file
            return new NoodlehausConfig($paths);

        // Only the base file should throw an exception. This is bad,
        // but should not break the system, so only log the error.
        } catch(FileNotFoundException $e) {
            error_log(sprintf(
                'WARNING! Failed to read config file at "%s".',
                $paths[0]
            ));

            throw $e;
        }
    }
}
