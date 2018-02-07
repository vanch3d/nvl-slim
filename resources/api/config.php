<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 15:55
 */

require_once __DIR__ . './../../vendor/autoload.php';

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

try {
    (new Dotenv(__DIR__ . './../../'))->load();
} catch (InvalidPathException $e) {
    error_log('[ERROR] config : ' . $e->getMessage());
    die();
}

try {
    $config = require_once __DIR__ . './../../sources/config.php';
    $config = $config['settings']['nvl-slim']['swagger'];
    if ($config === null) throw new Exception('settings/nvl-slim/swagger does not exist');
} catch (Exception $e) {
    error_log('[ERROR] config : ' . $e->getMessage());
    die();
}


define("SWAGGER_VERSION",$config['version']);
define("API_NAME","Swagger " . $config['api']['name']);
define("API_VERSION",$config['api']['version']);

error_log("=> Parsing " . API_NAME . " / " . API_VERSION);