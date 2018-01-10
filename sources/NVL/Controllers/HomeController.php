<?php 

namespace NVL\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @package NVL\Controllers
 */
class HomeController extends Controller {

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        dump($request);
        echo "this is the route for '/'";
    }

}