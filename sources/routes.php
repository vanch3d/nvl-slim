<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:20
 */

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
    dump("this is the route for: $pattern");
    $response = $next($request, $response);
    return $response;
};

// Middleware for adding debug information to all (json) output
$mwJSON = function (Request $request, Response $response, $next) {
    /** @var Route $route */
    $route = $request->getAttribute("route");
    $pattern = $route->getPattern();
    $response = $next($request, $response);
    $requestobject = json_decode($response->getBody()->__toString());
    $requestobject->debug = "this is the route for: $pattern";
    return $response->withJson($requestobject);
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
        $this->get('/map', ProjectController::class . ':storyMap')->setname('project.story');
        $this->get('/{name}', ProjectController::class . ':showProject')->setname('project.named');
        $this->get('/{name}/cloud', ProjectController::class . ':wordCloud')->setname('project.named.cloud');
    });

    // Publication-related routes
    $this->group('publications',function() {
        $this->get('', PublicationController::class . ':allPublications')->setname('publications.all');
        //$this->get('/graph', PublicationController::class . ':pubGraph')->setname('publications.all.graph');
        $this->get('/network', PublicationController::class . ':pubNetwork')->setname('publications.all.network');        // @todo[vanch3d] CHANGED NAME
        $this->get('/narrative', PublicationController::class . ':pubNarrative')->setname('publications.all.narrative');
        $this->get('/{name}.pdf', PublicationController::class . ':pubExportPDF')->setname('publications.named.pdf');
        $this->get('/{name}.txt', PublicationController::class . ':pubExportTXT')->setname('publications.named.txt');
        $this->get('/{name}', PublicationController::class . ':pubReader')->setname('publications.named.pubReader');     // @todo[vanch3d] CHANGED NAME
        $this->get('/{name}/show', PublicationController::class . ':pubShow')->setname('publications.named.show');
        $this->get('/{name}/cloud', PublicationController::class . ':pubDistrib')->setname('publications.named.cloud');    // @todo[vanch3d] CHANGED NAME
        $this->get('/{name}/assets/{fig}', PublicationController::class . ':pubAssets')->setname('publications.named.assets');
    });

    // Others routes
    $this->get('docs/{file}.pdf', PublicationController::class . ':redirectLegacy')->setname('publications.named.legacy');

})->add($mwHTML);

/**
 * Api Routes
 */
$app->group('/api',function() {
    $this->get('', APIController::class . ':apiHome')->setname('api.home');
    $this->get('/unapi', APIController::class . ':unAPI')->setname('api.unapi');
    $this->get('/projects', APIController::class . ':getAllProjects')->setname('api.projects');
    $this->get('/projects/{name}', APIController::class . ':getProject')->setname('api.projects.named');
    $this->get('/projects/{name}/publications', APIController::class . ':getPublications')->setname('api.projects.pub');
    $this->get('/projects/{name}/slides', APIController::class . ':getSlides')->setname('api.projects.slide');
    $this->get('/projects/{name}/images', APIController::class . ':getImages')->setname('api.projects.image');
    $this->get('/publications', APIController::class . ':getAllPublications')->setname('api.publicationss');
})->add($mwJSON);


/**
 * Global Middlewares
 */

