<?php
session_cache_limiter(false);
session_start();


require '../vendor/autoload.php';


// System's constants
define('APPLICATION', 'nvl-slim');
define('VERSION', '0.1');
define('EXT', '.twig');

// Prepare the Slim core app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
	'debug' => true
));
$app->config(require "../app/config.php");


// Create monolog logger and store logger in container as singleton
// (Singleton resources retrieve the same log resource definition each time)
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('nvl-slim');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../.logs/app.log', \Monolog\Logger::DEBUG));
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../.logs/error.log', \Monolog\Logger::ERROR));
    return $log;
});

// Prepare Twig view
$twig = new \Slim\Views\Twig();
$twig->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../.cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true,
	'debug' => true
);
$twig->parserExtensions = array(
		new \Slim\Views\TwigExtension(),
		new Twig_Extension_Debug()
);
$app->view($twig);

// Create a hook to add the root URI to all views
$app->hook('slim.before.dispatch', function() use ($app) {
	$app->view()->appendData(array(
			'app_base' => $app->request()->getUrl()
	));
});


// Define the routes
$app->get("/",function(){ echo "hello world"; });


// Run app
$app->run();