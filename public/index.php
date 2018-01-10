<?php

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

session_cache_limiter(false);
session_start();

//date_default_timezone_set('Europe/London');
//setlocale (LC_TIME, 'en_GB.UTF8','en_GB');

require '../vendor/autoload.php';


try {
    (new Dotenv(__DIR__ . '/../'))->load();
} catch (InvalidPathException $e) {
    die($e);
}


require_once __DIR__ . './../sources/helpers.php';

$config = require_once __DIR__.'./../sources/config.php';
$app = new \NVL\App($config);
$container = $app->getContainer();

require_once __DIR__ . '/../sources/routes.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    throw new Exception("dvfdf");
    $response->getBody()->write("Hello, $name");

    return $response;
});
$app->run();


