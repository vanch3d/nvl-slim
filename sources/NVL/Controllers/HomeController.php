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
    public function home(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function aboutMe(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function aboutSite(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function search(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

}