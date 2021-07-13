<?php

/**
 * This file is part of the Vökuró.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Vokuro\Cli;
use Phalcon\Exception as PhalconException;

error_reporting(E_ALL);
$rootPath = __DIR__;

try {
    require_once $rootPath . '/vendor/autoload.php';

    $arguments = [];
    foreach ($argv as $k => $arg) {
        if ($k === 1) {
            $arguments['task'] = $arg;
        } elseif ($k === 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
            $arguments['params'][] = $arg;
        }
    }
    /**
     * Load .env configurations
     */
    Dotenv\Dotenv::create($rootPath)->load();

    $cli = new Cli($rootPath, $arguments);

    /**
     * Run Vökuró!
     */
    $cli->run();
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
    exit(1);
}
