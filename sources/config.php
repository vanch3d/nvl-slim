<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:20
 */

return [

    'settings.httpVersion' => '1.1',
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => true,
    'settings.displayErrorDetails' => getenv('APP_DEBUG') === 'true',

    // TWIG settings
    'twig' => [
        'debug' => true,
        'cache' => __DIR__ . '/../.cache/',
    ]

];