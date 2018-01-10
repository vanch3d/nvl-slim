<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:20
 */

return [
    // SLIM settings
    'settings' => [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => getenv('APP_DEBUG') === 'true',
    ],

    // LOGGER settings
    'logger' => [
        'directory' => '/../.logs',
        'filename' => 'my-app.log',
        'timezone' => 'Europe/London',
        'level' => 'debug',
        'handlers' => []
    ],

    // TWIG settings
    'twig' => [
        'debug' => getenv('APP_DEBUG') === 'true',
        'cache' => __DIR__ . '/../.cache/',
    ]

];