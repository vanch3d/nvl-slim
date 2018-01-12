<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 23:58
 */

namespace NVL\Controllers;
use Interop\Container\ContainerInterface;
use NVL\Data\ProjectManager;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ProjectController
 * @package NVL\Controllers
 */
class ProjectController extends Controller
{

    public function allProjects(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function storyMap(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function showProject(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function wordCloud(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }
}