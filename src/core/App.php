<?php

namespace Schakel\Core;

use DateTimeZone;

/**
 * Contains constants of frequently used strings, numbers and class names.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
final class App {

    const MODE_DEV = 1;
    const MODE_TEST = 2;
    const MODE_PROD = 3;

    /**
     * @return Siero\Core\App
     */
    public static function getInstance() {
        static $inst;

        if (!$inst) {
            $inst = new self();
        }

        return $inst;
    }

    /**
     * Logs a message to the specified logger.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function log($level, $message, array $context = []) {
        if (self::getInstance()->getLogger() !== null) {
            self::getInstance()->getLogger()->log($level, $message, $context);
        }
    }

    /**
     * Returns the mode the app is in.
     *
     * @return int Mode, see MODE_ constants.
     */
    public static function getMode() {
        static $mode;

        if ($mode !== null) {
            return $mode;
        }

        if (!($mode = getenv('APP_MODE'))) {
            $cfg = Config::get('settings');
            $mode = $cfg->get('mode', 'dev');
        }

        switch ($mode) {
            case 'prod':
            case 'production':
                $mode = self::MODE_PROD;
                break;

            case 'test':
            case 'testing':
                $mode = self::MODE_TEST;
                break;

            default:
                $mode = self::MODE_DEV;
        }

        return $mode;
    }

    /**
     * @var string
     */
    private static $rootPath = false;

    /**
     * Returns root of this plugin, which is the /src/ directory.
     *
     * @return string Path without trailing /
     */
    public static function getFoundationPath() {
        return realpath(__DIR__ . '/../');
    }

    /**
     * @return string|null Path without trailing /, or null if not available
     */
    public static function getRootPath() {
        if(self::$rootPath !== false) {
            return self::$rootPath;
        }

        self::$rootPath = self::getFoundationPath();

        $path = __DIR__;
        $limit = 10;
        while(dirname($path) !== $path && 0 < ($limit--)) {
            if(basename($path) === 'vendor') {
                self::$rootPath = $path;
                break;
            }
        }

        return self::$rootPath;
    }

    /**
     * Sets root path
     * @param string $path
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public static function setRootPath($path) {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected path to be a string, got %s.', gettype($path)
            ));
        }

        if (!file_exists($path) || !is_dir($path)) {
            throw new \InvalidArgumentException(sprintf(
                "Expected a valid root path, but {$path} isn't one."
            ));
        }

        self::$rootPath = realpath($path) . DIRECTORY_SEPARATOR;
        return true;
    }

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Sets the logger for the whole application
     *
     * @param Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @return Psr\Log\LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
    }

}
