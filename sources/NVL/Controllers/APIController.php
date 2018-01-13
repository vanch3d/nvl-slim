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
        return $response->withJson([ 'message' => 'Nothing to see here'], 200);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function unAPI(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'error' => 'not yet implemented'], 403);

    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function getAllProjects(Request $request, Response $response, array $args)
    {
        $projects = [];
        try {
            $projects = $this->getProjectManager()->getData(false);
        } catch (\Exception $e) {
            $response = $response->withStatus(202,"Could not access list of projects");
        }
        return $response->withJson(['projects' => $projects]);

    }

    public function getAllPublications(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'error' => 'not yet implemented'], 403);

    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function getProject(Request $request, Response $response, array $args)
    {
        $project = $this->getProjectManager()->isDefined($args["name"]);
        if ($project)
            return $response->withJson([ 'project' => $project], 200);
        else
            return $response->withJson(
                ['error' => 'The project "' . $args["name"] . '" does not exist'],
                404);

    }

    public function getPublications(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'error' => 'not yet implemented'], 403);

    }

    public function getSlides(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'error' => 'not yet implemented'], 403);

    }

    public function getImages(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $response->withJson([ 'error' => 'not yet implemented'], 403);

    }

}