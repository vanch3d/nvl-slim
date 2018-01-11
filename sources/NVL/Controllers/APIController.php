<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 00:00
 */

namespace NVL\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class APIController
 * @package NVL\Controllers
 */
class APIController extends Controller
{

    public function apiHome(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

    public function unAPI(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

    public function getAllProjects(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

    public function getAllPublications(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

    public function getProject(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

    public function getPublications(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

    public function getSlides(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

    public function getImages(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'foo' => 'bar'], 200);

    }

}