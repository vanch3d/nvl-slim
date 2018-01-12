<?php 

namespace NVL\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @package NVL\Controllers
 */
class HomeController extends Controller
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function home(Request $request, Response $response, array $args)
    {
        $prjIdx = [];
        $publications = [];
        try {
            $prjIdx = $this->getProjectManager()->getData(true);
            $pubs = $this->getPublicationManager()->getData("all", 4);
            $publications = $pubs['publications'] ?? [];
        } catch (\Exception $e) {
            $response = $response->withStatus(202,"Could not access list of publications or projects");
        }
        dump($response);
        return $this->getView()->render($response, 'pages/home.twig',array(
            'projects' => $prjIdx,
            'publications' => $publications
        ));
    }

    public function aboutMe(Request $request, Response $response, array $args)
    {
        return $this->getView()->render($response, 'pages/about.twig');
    }

    public function aboutSite(Request $request, Response $response, array $args)
    {
        return $this->getView()->render($response, 'pages/about.site.twig');
    }

    public function search(Request $request, Response $response, array $args)
    {
        try {
            $projIdx = $this->getProjectManager()->getData(true);
        } catch (\Exception $e) {
            $response = $response->withStatus(202,"Could not access the list of projects");
        }
        return $this->getView()->render($response, 'pages/search.twig',array(
            'projects' => $projIdx
        ));
    }

}