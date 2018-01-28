<?php

use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Route;

/**
 * Created by PhpStorm.
 * User: nicol
 * Date: 12/12/2017
 * Time: 21:02
 */

/**
 * Class ControllerTest
 *
 * Check these:
 * https://github.com/andela-joyebanji/NaijaEmoji/tree/develop/test
 */
class ControllerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \NVL\App $app */
    private static $app = null;


    /**
     *
     */
    public static function setUpBeforeClass()
    {
        require_once __DIR__ . './../sources/helpers.php';
        $config = require_once __DIR__.'./../sources/config.php';
        $app = new \NVL\App($config);

        require_once __DIR__.'./../sources/dependencies.php';
        require_once __DIR__ . './../sources/routes.php';

        self::$app = $app;
    }

    public function testAppInitialised()
    {
        $this->assertTrue(self::$app !== null);
    }

    public function testAllRoutesHandled()
    {
        /** @var Route[] $routes */
        $routes = self::$app->getContainer()->get('router')->getRoutes();
        foreach ($routes as $r)
        {
            $pat = $r->getPattern();
            $env = Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI'    => str_replace(["{","}"],"",$pat)
            ]);
            $req = Request::createFromEnvironment($env);
            self::$app->getContainer()['request'] = $req;
            $response = self::$app->run(true);
            $this->assertNotSame($response->getStatusCode(), 500);
        }
    }

}

