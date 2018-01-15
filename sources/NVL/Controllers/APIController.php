<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 00:00
 */

namespace NVL\Controllers;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Requests;
use SimpleXMLElement;
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
        $err = [
            'status'    => 400,
            'title'     => 'not implemented',
            'details'   => 'Nothing to see here'
        ];
        $errs[]=$err;
        return $response->withJson(['errors' => $errs], 400);
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
        $err = [
            'status'    => 400,
            'title'     => 'not implemented',
            'details'   => 'Nothing to see here'
        ];
        $errs[]=$err;
        return $response->withJson(['errors' => $errs], 400);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function getAllProjects(Request $request, Response $response, array $args)
    {
        try {
            $projects = $this->getProjectManager()->getData(false);
            return $response->withJson(['data' => $projects]);

        } catch (\Exception $e) {
            $err = [
                'status'    => 500,
                'title'     => 'internal error',
                'details'   => 'could not access the list of projects'
            ];
            $errs[]=$err;
            return $response->withJson(['errors' => $errs], 500);
        }
    }

    public function getAllPublications(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        $err = [
            'status'    => 400,
            'title'     => 'not implemented',
            'details'   => 'Nothing to see here'
        ];
        $errs[]=$err;
        return $response->withJson(['errors' => $errs], 400);
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
        if (false === $project) {
            $err = [
                'status'    => 404,
                'title'     => 'not found',
                'details'   => 'The project does not exist'
            ];
            $errs[]=$err;
            return $response->withJson(['errors' => $errs], $err['status']);
        }
        return $response->withJson(['data' => $project], 200);

    }

    public function getPublications(Request $request, Response $response, array $args)
    {
        $project = $this->getProjectManager()->isDefined($args["name"]);
        if (false === $project) {
            $err = [
                'status'    => 404,
                'title'     => 'not found',
                'details'   => 'The project does not exist'
            ];
            $errs[]=$err;
            return $response->withJson(['errors' => $errs], $err['status']);
        }

        $res = $this->getPublicationManager()->getData($args["name"]);
        $pubs = $res['publications'];
        unset($res['publications']);
        return $response->withJson([ 'data' => $pubs,'meta'=>$res]);

    }

    public function getSlides(Request $request, Response $response, array $args)
    {
        // set the cache for the request
        //$this->app->etag('nvl-slim.slideshare'.$name);
        //$this->app->expires('+1 week');

        $project = $this->getProjectManager()->isDefined($args["name"]);
        if (false === $project) {
            $err = [
                'status'    => 404,
                'title'     => 'not found',
                'details'   => 'The project does not exist'
            ];
            $errs[]=$err;
            return $response->withJson(['errors' => $errs], $err['status']);
        }

        try {
            $settings = $this->getSettings();
            $cfg = $settings['nvl-slim']['slideshare'];
            if (!isset($cfg))
                throw new \Exception("SlideShare API authentication not configured", 500);

            $ts = time();
            $data = array(
                'ts' => $ts,                        // timestamp
                'api_key' => $cfg['api_key'],            // SlideShare API key
                'hash' => sha1($cfg['secret'] . $ts),   // Secret hash
                'username_for' => 'nicolasVL',                // Slides owner
                'detailed' => '1'
            );// build the URL for Slideshare API
            $param = http_build_query($data);
            $url2 = 'https://www.slideshare.net/api/2/get_slideshows_by_user?' . $param;
            $request = Requests::get($url2);
            if (!$request->success)
                throw new \Exception('Cannot access the SlideShare API.', 500);
            $xml = new SimpleXMLElement($request->body);
            if ("SlideShareServiceError" == $xml->getName()) {
                $node = $xml->children();
                $attr = $node->attributes();
                throw new \Exception("SlideShareServiceError : " . (string)$node, 500);//(string)$attr->ID);
            }
            $text = json_encode($xml);
            $json = json_decode($text, true);
            $slides = array();
            foreach ($json['Slideshow'] as $slideshow) {
                if (in_array("@" . $args['name'], $slideshow['Tags']['Tag']))
                    $slideshow['type'] = 'slide';
                    $slides[] = $slideshow;
            }
            $json['Count'] = count($slides);
            unset($json['Slideshow']);

            return $response->withJson([ 'data' => $slides,'meta'=> $json]);

        } catch (\Exception $e) {

            $err = [
                'status'    => $e->getCode(),
                'title'     => 'internal error',
                'details'   => $e->getMessage()
            ];
            $errs[]=$err;
            return $response->withJson(['errors' => $errs], $e->getCode());

        }
    }

    public function getImages(Request $request, Response $response, array $args)
    {
        // set the cache for the request
        //$this->app->etag($name.'gallery');
        //$this->app->expires('+1 week');

        $project = $this->getProjectManager()->isDefined($args["name"]);
        if (false === $project) {
            $err = [
                'status'    => 404,
                'title'     => 'not found',
                'details'   => 'The project does not exist'
            ];
            $errs[]=$err;
            return $response->withJson(['errors' => $errs], $err['status']);
        }

        $data = array(
            'format'=>		'json',					// Zotero API key
            'method'=>		'pwg.categories.getList',	// Zotero API key
        );

        // build the URL for galery API
        $param = http_build_query($data);
        $url2 = 'http://gallery.calques3d.org/ws.php?'.$param;

        try {

            $request = Requests::get($url2);
            $data = json_decode($request->body, true);

            if (isset($data['err'])) {
                throw new \Exception($data['message'], $data['err']);
            }

            $name = $args['name'];
            $cat = array_filter($data['result']['categories'], function ($var) use ($name) {
                return ($name == $var['permalink']);
            });
            $cat = reset($cat);

            if (!$cat) {
                //throw new \Exception('No images for this project', 501);
                return $response->withJson([ 'data' => []]);
            }


            $data = array(
                'format' => 'json', // Zotero API key
                'method' => 'pwg.categories.getImages', // Zotero API key
                'cat_id' => $cat['id']
            );

            // build the URL for Zotero API
            $param = http_build_query($data);
            $url2 = 'http://gallery.calques3d.org/ws.php?' . $param;

            $request = Requests::get($url2);
            $data = json_decode($request->body, true);

            if (isset($data['err'])) {
                throw new \Exception($data['message'], $data['err']);
            }


            $imgs = array();
            foreach ($data['result']['images'] as $img) {
                $tmp['type'] = 'image';
                $tmp['id'] = $img['id'];
                $tmp['url'] = $img['element_url'];
                $tmp['comment'] = $img['comment'];
                $tmp['title'] = $img['name'];
                $tmp['thumb'] = $img['derivatives']['thumb']['url'];
                $imgs[] = $tmp;
            }
            //$this->outputJSON($imgs);

            return $response->withJson([ 'data' => $imgs]);
        }
        catch (\Exception $e)
        {
            $err = [
                'status'    => $e->getCode(),
                'title'     => 'internal error',
                'details'   => $e->getMessage()
            ];
            $errs[]=$err;
            return $response->withJson(['errors' => $errs], $e->getCode());
        }
    }

}