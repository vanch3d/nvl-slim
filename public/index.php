<?php
session_cache_limiter(false);
session_start();


require '../vendor/autoload.php';
require_once "../app/utils/libZoteroSingle.php";
require_once "../app/utils/CiteProc.php";
require_once "../app/application.php";
require_once "../app/controllers/home.controller.php";
require_once "../app/controllers/project.controller.php";
require_once "../app/controllers/api.controller.php";
require_once "../app/controllers/sandbox.controller.php";


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
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../.logs/app.log', \Monolog\Logger::NOTICE));
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
            //'app_base' => "http://nvl.calques3d.org"
	));
});

// Create the application core
$c = new Application($app);

// Create the controllers
$homeCtrl = new HomeController();
$projectCtrl = new ProjectController();
$apiCtrl = new APIController();
$sdxCtrl = new SandboxController();

// Define the routes
// site routes
$c->app->get('/', array($homeCtrl, 'home'))->name('home');
$c->app->get('/about/', array($homeCtrl, 'aboutMe'))->name('about.me');
$c->app->get('/about/site', array($homeCtrl, 'aboutSite'))->name('about.site');
$c->app->get('/search', array($homeCtrl, 'search'))->name('search');

// project routes
$c->app->get('/projects/', array($projectCtrl, 'allProjects'))->name('project.all');
$c->app->get('/projects/map/', array($projectCtrl, 'storyMap'))->name('project.story');
$c->app->get('/projects/:name/', array($projectCtrl, 'project'))->name('project.named');
$c->app->get('/projects/:name/cloud', array($projectCtrl, 'wordCloud'))->name('project.named.cloud');

// publication routes
$c->app->get('/publications/', array($projectCtrl, 'allPublications'))->name('publications.all');
//$c->app->get('/publications/graph/', array($projectCtrl, 'pubGraph'))->name('publications.all.graph');
$c->app->get('/publications/network/', array($projectCtrl, 'pubMap'))->name('publications.all.map');
$c->app->get('/publications/narrative/', array($sdxCtrl, 'pubNarrative'))->name('publications.all.narrative');
$c->app->get('/publications/:name.pdf', array($projectCtrl, 'pubExportPDF'))->name('publications.named.pdf');
$c->app->get('/publications/:name.txt', array($projectCtrl, 'pubExportTXT'))->name('publications.named.txt');
$c->app->get('/publications/:name/', array($projectCtrl, 'pubReader'))->name('publications.named.pubreader');
$c->app->get('/publications/:name/show/', array($projectCtrl, 'pubShow'))->name('publications.named.show');
$c->app->get('/publications/:name/assets/:fig', array($projectCtrl, 'pubAssets'))->name('publications.named.assets');
$c->app->get('/publications/:name/cloud/', array($projectCtrl, 'pubDistrib'))->name('publications.named.freqdist');

// other routes
$c->app->get('/docs/:file.pdf', array($sdxCtrl, 'redirectLegacy'))->name('sandbox.legacy');
$c->app->get('/sandbox/isotope', array($sdxCtrl, 'getIsotope'))->name('sandbox.isotope');
$c->app->get('/sandbox/test', function() use($apiCtrl) {
    // A quick & dirty route for quick & dirty tests
    $ret = $apiCtrl->getPublicationsJSON("2013.RANLP.Summarisation");
    $apiCtrl->outputJSON($ret);
});


// api routes
$c->app->get('/api/unapi', array($apiCtrl, 'unAPI'))->name('api.unapi');
$c->app->get('/api/projects/', array($apiCtrl, 'getAllProjectJSON'))->name('api.projects');
$c->app->get('/api/projects/:name/', array($apiCtrl, 'getProjectJSON'))->name('api.project.named');
$c->app->get('/api/projects/:name/publications', array($apiCtrl, 'getPublicationsJSON'))->name('api.pub.project');
$c->app->get('/api/projects/:name/slides', array($apiCtrl, 'getSlidesJSON'))->name('api.slide.project');
$c->app->get('/api/projects/:name/images', array($apiCtrl, 'getImagesJSON'))->name('api.images.project');

//$c->app->error(array($homeCtrl, 'showError'));
$c->app->notFound(array($homeCtrl, 'showNotFound'));

// Run app
$app->run();