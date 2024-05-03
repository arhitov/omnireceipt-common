<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');

if (! function_exists('dump')) {
    function dump(): void
    {
        $backtraceList = debug_backtrace(limit: 2);
        do {
            $backtrace = $backtraceList[0];
            [
                'file' => $file,
                'line' => $line,
            ] = $backtrace ?? ['file' => 'undefined', 'line' => 'undefined'];
            $backtraceList = array_slice($backtraceList, 1);
        } while ($file === __FILE__);
        echo "\n" . $file . ':' . $line . "\n";

        foreach (func_get_args() as $arg) {
            var_dump($arg);
            echo "\n";
        }
    }
}
if (! function_exists('dd')) {
    function dd(): void
    {
        dump(...func_get_args());
        exit;
    }
}

require_once __DIR__ . '/../vendor/autoload.php';
