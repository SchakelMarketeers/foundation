<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Controller;

use Schakel\Core\Twig;
use Schakel\Core\Json;

use Schakel\Core\App;

/**
 * Returns an error handler. Required by URL handler in index.php.
 * DO NOT REMOVE!
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class ErrorController {

    /**
     * @param Exception $trace Exception to trace
     * @return array
     */
    public static function formatTrace(\Exception $trace) {
        $out = [];

        foreach($trace->getTrace() as $entry) {

            if(!empty($entry['class']) && !empty($entry['function'])) {
                $func = $entry['class'] . $entry['type'] .  $entry['function'];
            } elseif(!empty($entry['function'])) {
                $func = $entry['function'];
            } else {
                $func = null;
            }

            if(empty($entry['file']))
                $entry['file'] = '<< unknown >>';

            if(!empty($entry['line'])) {
                $source = "{$entry['file']}:{$entry['line']}";
            } else {
                $source = $entry['file'];
            }

            if($func == null) {

            } else {
                $args = [];
                foreach($entry['args'] as $arg) {
                    if(is_object($arg) && $arg instanceof \Countable) {
                        $args[] = get_class($arg) . '[' . count($arg) . ']';
                    } elseif(is_object($arg) && $arg instanceof \Countable) {
                        $args[] = get_class($arg);
                    } elseif(is_array($arg)) {
                        $args[] = 'array[' . count($arg) . ']';
                    } elseif(is_string($arg)) {
                        $args[] = 'string[' . strlen($arg) . ']';
                    } else {
                        $args[] = gettype($arg);
                    }
                }

                $isUserFunc = preg_match('/^call_user_func(_array)?$/', $func);

                if($isUserFunc && is_array($entry['args'][0])) {
                    $args[0] = sprintf(
                        '[%s, "%s"]',
                        get_class($entry['args'][0][0]),
                        $entry['args'][0][1]
                    );
                } elseif($isUserFunc) {
                    $args[0] = $entry['args'][0];
                }

                $out[] = sprintf(
                    '<code>%s(%s)</code> in <code>%s</code>',
                    $func, implode(', ', $args), $source
                );
            }
        }

        return $out;
    }

    /**
     * Renders an error. Mandatory method, DO NOT REMOVE.
     *
     * @param array|Exception $args
     * @return AbstractRenderer
     */
    public function renderError($args) {
        $opMode = App::getOpMode();

        $trace = null;

        if(is_object($args) && $args instanceof \Exception) {
            $error = [
                'type' => get_class($args),
                'msg' => $args->getMessage(),
                'code' => $args->getCode()
            ];
            $trace = self::formatTrace($args);
        } elseif(isset($args['error'])) {
            $error = $args['error'];
        } else {
            $error = [
                'type' => 'FileNotFoundException',
                'msg' => 'File not found',
                'code' => 404
            ];
        }

        $http = $error['code'] == 404 ? 404 : 500;

        if($opMode == App::OPMODE_WEB) {
            $render = new Twig("{$http}.twig", $http);
        } else {
            $render = new Json($http);
            $render->setPrettyPrint($opMode == App::OPMODE_CLI);
        }

        $render->setArgs([
            'error' => $error,
            'error_trace' => $trace
        ]);

        return $render;
    }

}
