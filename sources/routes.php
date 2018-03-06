<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:20
 */

use Tracy\Debugger;
use NVL\Controllers\APIController;
use NVL\Controllers\HomeController;
use NVL\Controllers\ProjectController;
use NVL\Controllers\PublicationController;
use Slim\Http\Request;
use Slim\Http\Response;

// Middleware for adding debug information to all (html) output
$mwHTML = function (Request $request, Response $response, $next) {
    /** @var Route $route */
    $route = $request->getAttribute("route");
    $pattern = $route->getPattern();
    Debugger::barDump("this is the route for: $pattern");

    $response = $next($request, $response);
    return $response;
};

// Middleware for adding debug information to all (json/xml) output
$mwAPI = function (Request $request, Response $response, $next) {
    /** @var Route $route */
    $route = $request->getAttribute("route");
    $pattern = $route->getPattern();
    $request = $request->withAttribute('meta', ['debug'=>['route' => $pattern]]);

    $response = $next($request, $response);
    return $response;
};

/**
 * Web App Routes
 */
$app->group('/',function() {

    // Main routes
    $this->get('', HomeController::class . ':home')->setname('home');
    $this->get('about', HomeController::class . ':aboutMe')->setname('about.me');
    $this->get('about/site', HomeController::class . ':aboutSite')->setname('about.site');
    $this->get('search', HomeController::class . ':search')->setname('search');

    // Project-related routes
    $this->group('projects',function() {
        $this->get('', ProjectController::class . ':allProjects')->setname('project.all');
        $this->get('/github', ProjectController::class . ':showGitHub')->setname('project.github');
        $this->get('/storymap', ProjectController::class . ':storyMap')->setname('project.story');
        $this->get('/{name}', ProjectController::class . ':showProject')->setname('project.named');
        $this->get('/{name}/cloud', ProjectController::class . ':wordCloud')->setname('project.named.cloud');
    });

    // Publication-related routes
    // @todo[vanch3d] Handle {name}.{EXT} with parameters and/or Content-Type
    $this->group('publications',function() {
        $this->get('', PublicationController::class . ':allPublications')->setname('publications.all');
        //$this->get('/graph', PublicationController::class . ':pubGraph')->setname('publications.all.graph');
        $this->get('/network', PublicationController::class . ':pubNetwork')->setname('publications.all.network');
        $this->get('/narrative', PublicationController::class . ':pubNarrative')->setname('publications.all.narrative');
        $this->get('/{name}.pdf', PublicationController::class . ':pubExportPDF')->setname('publications.named.pdf');
        $this->get('/{name}.txt', PublicationController::class . ':pubExportTXT')->setname('publications.named.txt');
        $this->get('/{name}.md', PublicationController::class . ':pubExportHTML')->setname('publications.named.txt');
        $this->get('/{name}', PublicationController::class . ':pubReader')->setname('publications.named.pubReader');
        $this->get('/{name}/show', PublicationController::class . ':pubShow')->setname('publications.named.show');
        $this->get('/{name}/cloud', PublicationController::class . ':pubDistribution')->setname('publications.named.cloud');
        $this->get('/{name}/assets/{fig}', PublicationController::class . ':pubAssets')->setname('publications.named.assets');
    });

    // Others routes
    $this->get('docs/{file}.pdf', PublicationController::class . ':redirectLegacy')->setname('publications.named.legacy');

    // swagger-ui routes
    $this->get('api', APIController::class . ':apiHome')->setname('api.home');
    $this->get('api/openapi.json', APIController::class . ':getSwagger')->setname('api.swagger');

})->add($mwHTML);

/**
 * Api Routes
 */
$app->group('/api',function() {
    $this->get('/unapi', APIController::class . ':unAPI')->setname('api.unapi');
    $this->get('/projects', APIController::class . ':getAllProjects')->setname('api.projects');
    $this->get('/projects/{name}', APIController::class . ':getProject')->setname('api.projects.named');
    $this->get('/projects/{name}/publications', APIController::class . ':getPublications')->setname('api.projects.pub');
    $this->get('/projects/{name}/slides', APIController::class . ':getSlides')->setname('api.projects.slide');
    $this->get('/projects/{name}/images', APIController::class . ':getImages')->setname('api.projects.image');
    $this->get('/projects/{name}/repos', APIController::class . ':getRepositories')->setname('api.projects.repos');
    $this->get('/publications', APIController::class . ':getAllPublications')->setname('api.publications');
})->add($mwAPI);


/**
 * Global Middlewares
 */

$app->add(new \RunTracy\Middlewares\TracyMiddleware($app));
$app->add(new \Slim\HttpCache\Cache('public', 86400));



