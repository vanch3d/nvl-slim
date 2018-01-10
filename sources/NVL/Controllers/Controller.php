<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:35
 */

namespace NVL\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

abstract class Controller
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @todo[vanch3d] A temporary 'catchall' route. To be removed when all routes defined
     */
    public function _default(Request $request, Response $response, array $args)
    {
        /** @var Route $req */
        $req = $request->getAttribute("route");
        $pattern = $req->getPattern();
        dump("this is the route for $pattern");
    }


}