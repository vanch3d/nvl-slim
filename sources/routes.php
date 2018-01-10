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

/**
 * Web App Routes
 */
$app->group('/',function() {

    // Main routes
    $this->get('', [HomeController::class, '_default'])->setname('home');
    $this->get('about', [HomeController::class, '_default'])->setname('about.me');
    $this->get('about/site', [HomeController::class, '_default'])->setname('about.site');
    $this->get('search', [HomeController::class, '_default'])->setname('search');

    // Project-related routes
    $this->group('projects',function() {
        $this->get('', [ProjectController::class, '_default'])->setname('project.all');
        $this->get('/map', [ProjectController::class, '_default'])->setname('project.story');
        $this->get('/{name}', [ProjectController::class, '_default'])->setname('project.named');
        $this->get('/{name}/cloud', [ProjectController::class, '_default'])->setname('project.named.cloud');
    });

    // Publication-related routes
    $this->group('publications',function() {
        $this->get('', [PublicationController::class, '_default'])->setname('publications.all');
        //$this->get('/graph', [PublicationController::class, '_default'])->setname('publications.all.graph');
        $this->get('/network', [PublicationController::class, '_default'])->setname('publications.all.network');        // @todo[vanch3d] CHANGED NAME
        $this->get('/narrative', [PublicationController::class, '_default'])->setname('publications.all.narrative');
        $this->get('/{name}', [PublicationController::class, '_default'])->setname('publications.named.pubReader');     // @todo[vanch3d] CHANGED NAME
        $this->get('/{name}.pdf', [PublicationController::class, '_default'])->setname('publications.named.pdf');
        $this->get('/{name}.txt', [PublicationController::class, '_default'])->setname('publications.named.txt');
        $this->get('/{name}/show', [PublicationController::class, '_default'])->setname('publications.named.show');
        $this->get('/{name}/cloud', [PublicationController::class, '_default'])->setname('publications.named.cloud');    // @todo[vanch3d] CHANGED NAME
        $this->get('/{name}/assets/{fig}', [PublicationController::class, '_default'])->setname('publications.named.assets');
    });

    // Others routes
    $this->get('docs/{file}.pdf', [PublicationController::class, '_default'])->setname('publications.named.legacy');

});

/**
 * Api Routes
 */
$app->group('/api',function() {
    $this->get('', [APIController::class, '_default'])->setname('api.home');
    $this->get('/unapi', [APIController::class, '_default'])->setname('api.unapi');
    $this->get('/projects', [APIController::class, '_default'])->setname('api.projects');
    $this->get('/projects/{name}', [APIController::class, '_default'])->setname('api.projects.named');
    $this->get('/projects/{name}/publications', [APIController::class, '_default'])->setname('api.projects.pub');
    $this->get('/projects/{name}/slides', [APIController::class, '_default'])->setname('api.projects.slide');
    $this->get('/projects/{name}/images', [APIController::class, '_default'])->setname('api.projects.image');
});


/**
 * Global Middlewares
 */

