<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 23:58
 */

namespace NVL\Controllers;

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
        $projects = [];
        try {
            $projects = $this->getProjectManager()->getData();
        } catch (\Exception $e) {

            debug($e);
            $response = $response->withStatus(202,"Could not access list of projects");
        }

        return $this->getView()->render($response, 'projects/index.twig',array(
            'projects' => $projects
        ));
    }

    public function storyMap(Request $request, Response $response, array $args)
    {
        $projects = [];
        try {
            $projects = $this->getProjectManager()->getData();
        } catch (\Exception $e) {
            $response = $response->withStatus(202,"Could not access list of projects");
        }

        return $this->getView()->render($response, 'projects/storymap.twig',array(
            'projects' => $projects
        ));
    }

    public function showProject(Request $request, Response $response, array $args)
    {
        //$projIdx = $this->isProjectDefined($name);
        $projIdx = $this->getProjectManager()->isDefined($args["name"]);
        if ($projIdx === false) {
            //$this->app->notFound();
            return $this->notFound($request,$response,new \Exception("Project does not exists"));
        }

        $publications = [];
        $cite = [];
        try {
            $pubs = $this->getPublicationManager()->getData($args["name"]);
            $publications = $pubs['publications'];
            foreach ($publications as $pub)
            {
                $cite[$pub['archive_location']] = $pub['output']['cite'];
            }

        } catch (\Exception $e) {
        }
        return $this->getView()->render($response,'projects/content/'.$args['name'].'.twig',
            array(
                'tmpl_base' => 'template.html.twig',
                'project' => $projIdx,
                'publications' => $publications,
                'cites' => $cite
        ));
    }

    public function wordCloud(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }
}