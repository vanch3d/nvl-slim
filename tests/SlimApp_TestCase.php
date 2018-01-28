<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 26/01/2018
 * Time: 15:05
 */

namespace NVL\Tests;

use NVL\App;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;

abstract class SlimApp_TestCase extends TestCase
{
    /**
     * Static App created once for all tests
     * @var App $appStatic
     */
    static private $appStatic = null;

    /**
     * App reset from static at each Test
     * @var App $app
     */
    protected $app = null;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // create the nvl-slim app wih all containers and routes
        require_once __DIR__ . './../sources/helpers.php';
        $config = require_once __DIR__.'./../sources/config.php';
        $app = new App((array)$config);

        require_once __DIR__.'./../sources/dependencies.php';
        require_once __DIR__ . './../sources/routes.php';

        self::$appStatic = $app;
    }


    protected function setUp()
    {
        parent::setUp();
        $this->app = self::$appStatic;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $data
     * @return Request
     */
    protected function getRequest(string $method, string $path, array $data = [])
    {
        $method = strtoupper($method);

        /** @var Request $request */
        $request = Request::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'QUERY_STRING' => ($method == "GET") ? http_build_query($data) : "",
        ]));

        $request = $request->withHeader('Content-Type', 'application/json');

        if ($method == "POST") {
            $request->getBody()->write(json_encode($data));
        }

        return $request;
    }

}