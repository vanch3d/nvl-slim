<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:20
 */

use RunTracy\Helpers\Profiler\Profiler;
use Tracy\Debugger;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
define('DIR', realpath(__DIR__ . '/../') . DS);
if (getenv('APP_DEBUG') === 'true')
{
    Debugger::$maxDepth = 5; // default: 3
    Debugger::$showLocation = true; // Shows all additional location information
    Debugger::enable(Debugger::DEVELOPMENT, DIR . '.logs');
    Profiler::enable();
}
return [
    'settings' => [

        // SLIM settings
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => (getenv('APP_DEBUG') === 'true'),
        'addContentLengthHeader' => false,

        // LOGGER settings
        'logger' => [
            'path' => __DIR__ . '/../.logs/slim-app.log',
            'name' => getenv('APP_NAME'),
            'maxFiles' => 14,
            'timezone' => 'Europe/London',
            'level' => Monolog\Logger::DEBUG
        ],

        // TWIG settings
        'view' => [
            'template_path' =>  __DIR__ . '/../resources/templates/',
            'twig' => [
                'debug' => (getenv('APP_DEBUG') === 'true'),
                'cache' => __DIR__ . '/../.cache/',
                'auto_reload' => (getenv('APP_DEBUG') === 'true'),
            ],
        ],

        'tracy' => [
            'showPhpInfoPanel' => 1,
            'showSlimRouterPanel' => 1,
            'showSlimEnvironmentPanel' => 1,
            'showSlimRequestPanel' => 1,
            'showSlimResponsePanel' => 1,
            'showSlimContainer' => 1,
            'showEloquentORMPanel' => 0,
            'showTwigPanel' => 1,
            'showIdiormPanel' => 0,// > 0 mean you enable logging
            // but show or not panel you decide in browser in panel selector
            'showDoctrinePanel' => 0,// here also enable logging and you must enter your Doctrine container name
            // and also as above show or not panel you decide in browser in panel selector
            'showProfilerPanel' => 1,
            'showVendorVersionsPanel' => 1,
            'showXDebugHelper' => 1,
            'showIncludedFiles' => 1,
            'showConsolePanel' => 1,
            'configs' => [
                // XDebugger IDE key
                'XDebugHelperIDEKey' => 'PHPSTORM',
                // Disable login (don't ask for credentials, be careful) values( 1 || 0 )
                'ConsoleNoLogin' => 0,
                // Multi-user credentials values( ['user1' => 'password1', 'user2' => 'password2'] )
                'ConsoleAccounts' => [
                    'dev' => '34c6fceca75e456f25e7e99531e2425c6c1de443'// = sha1('dev')
                ],
                // Password hash algorithm (password must be hashed) values('md5', 'sha256' ...)
                'ConsoleHashAlgorithm' => 'sha1',
                // Home directory (multi-user mode supported) values ( var || array )
                // '' || '/tmp' || ['user1' => '/home/user1', 'user2' => '/home/user2']
                'ConsoleHomeDirectory' => DIR,
                // terminal.js full URI
                'ConsoleTerminalJs' => '/assets/js/jquery.terminal.min.js',
                // terminal.css full URI
                'ConsoleTerminalCss' => '/assets/css/jquery.terminal.min.css',
                'ProfilerPanel' => [
                    // Memory usage 'primaryValue' set as Profiler::enable() or Profiler::enable(1)
//                    'primaryValue' =>                   'effective',    // or 'absolute'
                    'show' => [
                        'memoryUsageChart' => 1, // or false
                        'shortProfiles' => true, // or false
                        'timeLines' => true // or false
                    ]
                ]
            ]
        ],

        'nvl-slim' => [
            'swagger' => [
                'version' => "3.0.1",
                'output' => 'openapi.json',
                'api' => [
                    'name' => getenv('APP_NAME'),
                    'version' => "0.3"
                ]
            ],
            'slideshare' => [
                'url'       =>  getenv('SLIDESHARE_URL'), // URL for the Slideshare API
                'username'  =>  getenv('SLIDESHARE_USER'),  // username to retrieve slides from
                'api_key'   =>  getenv('SLIDESHARE_APIKEY'),     // SlideShare Personal API key
                'secret'    =>  getenv('SLIDESHARE_SECRET')      // SlideShare Shared Secret
            ],
            'zotero' => [
                'url'       =>  getenv('ZOTERO_URL'),    // URL of the Zotero user API
                'userID'    =>  getenv('ZOTERO_USERID'),                         // Zotero personal userID
                'api_key'   =>  getenv('ZOTERO_APIKEY'),     // Zotero API key for access to lib
                'collectID' =>  getenv('ZOTERO_COLLECTID')                      // Unique ID of the Zotero collection to access
            ]
        ]

    ]

];