<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 00:43
 */

namespace NVL\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;


class PublicationController extends Controller
{

    public function allPublications(Request $request, Response $response, array $args)
    {
        try {
            $pubs = $this->getPublicationManager()->getData("all", 100);
            $publications = $pubs['publications'] ?? [];
            $uniqueYears = array_unique(array_map(function($v){
                return $v['issued']['date-parts'][0][0];
            },$publications));
            $uniqueTypes = array_unique(array_map(function($v){
                return $v['type'];
            },$publications));

        } catch (\Exception $e) {
        }

        return $this->getView()->render($response, 'publications/showall.twig',array(
            'publications' => $publications ?? [],
            'years' => $uniqueYears ?? [],
            'types' => $uniqueTypes ?? []
        ));
    }

    public function pubNetwork(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubNarrative(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubReader(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubExportPDF(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubExportTXT(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubShow(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubDistrib(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubAssets(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function redirectLegacy(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

}