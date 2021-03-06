<?php

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

session_cache_limiter(false);
session_start();

//date_default_timezone_set('Europe/London');
//setlocale (LC_TIME, 'en_GB.UTF8','en_GB');

require './../vendor/autoload.php';


try {
    (new Dotenv(__DIR__ . '/../'))->load();
} catch (InvalidPathException $e) {
    die($e);
}

require_once __DIR__ . './../sources/helpers.php';

// Initiate the app with configuration
$config = require_once __DIR__.'./../sources/config.php';
$app = new \NVL\App($config);
Tracy\Debugger::barDump($config);

// set up containers
require_once __DIR__.'./../sources/dependencies.php';

// Set up routes & middleware
require_once __DIR__ . './../sources/routes.php';

// start the app
$app->run();


