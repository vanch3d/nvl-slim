<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:20
 */

return [
    'settings' => [

        // SLIM settings
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => getenv('APP_DEBUG') === 'true',

        // LOGGER settings
        'logger' => [
            'directory' => __DIR__ . '/../.logs',
            'filename' => 'my-app.log',
            'timezone' => 'Europe/London',
            'level' => 'debug',
            'handlers' => []
        ],

        // TWIG settings
        'view' => [
            'template_path' =>  __DIR__ . '/../resources/templates/',
            'twig' => [
                'debug' => getenv('APP_DEBUG') === 'true',
                'cache' => __DIR__ . '/../.cache/',
                'auto_reload' => getenv('APP_DEBUG') === 'true',
            ],
        ]
    ],

];