<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 00:00
 */

namespace NVL\Controllers;
use NVL\Support\ArrayToXml;
use Requests;
use SimpleXMLElement;
use Slim\Http\Body;
use Slim\Http\Request;
use Slim\Http\Response;
use Tracy\Debugger;

/**
 * Class APIController
 * @package NVL\Controllers
 *
 * @todo[vanch3d] Look at 'akrabat/rka-content-type-renderer' for a better alternative?
 */
class APIController extends Controller
{
    const
        API_JSON = 'application/json',
        API_XML = 'application/xml';

    const
        ERR_NOTIMPLEMENTED = "not yet implement",
        ERR_INTERNALERROR = "internal error",
        ERR_BADREQUEST = "bad request",
        ERR_NOTFOUND = "not found";

    private $checkHeader = true;
    private $outputParam = 'output';
    private $xmlRootName = "nvl-slim-api";
    private $unapiRootName = "formats";
    private $defaultMediaType = APIController::API_JSON;
    private $knownMediaTypes = [
        APIController::API_JSON,
        APIController::API_XML,
        'text/xml'
    ];

    /**
     * Getter for defaultMediaType
     *
     * @return string
     */
    protected function getDefaultMediaType()
    {
        return $this->defaultMediaType;
    }

    /**
     * Setter for defaultMediaType
     *
     * @param string $defaultMediaType Value to set
     * @return self
     */
    protected function setDefaultMediaType(string $defaultMediaType)
    {
        $this->defaultMediaType = $defaultMediaType;
        return $this;
    }

    /**
     * Read the output parameter or accept header and determine which media type we know about
     * is wanted.
     *
     * @param Request $request
     * @return string
     */
    protected function determineMediaType(Request $request)
    {
        $output = $request->getQueryParam($this->outputParam);
        if ($output !== null) {
            if (mb_strtolower($output) == 'xml') {
                return APIController::API_XML;
            }
            return APIController::API_JSON;
        }
        if ($this->checkHeader) {
            $acceptHeader = $request->getHeaderLine('Accept');
            if (!empty($acceptHeader)) {
                $selectedMediaTypes = array_intersect(explode(',', $acceptHeader), $this->knownMediaTypes);
                if (count($selectedMediaTypes)) {
                    return current($selectedMediaTypes);
                }
                // handle +json and +xml specially
                if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
                    $mediaType = 'application/' . $matches[1];
                    if (in_array($mediaType, $this->knownMediaTypes)) {
                        return $mediaType;
                    }
                }
            }
        }
        return $this->getDefaultMediaType();
    }

    /**
     * Render the data using the format determined by request header or parameter
     * @param Request  $request
     * @param Response $response
     * @param array    $data
     * @param int      $status
     * @param string   $forceFormat
     * @return Response
     * @throws \Exception
     */
    protected function render(Request $request, Response $response, array $data,int $status=200,string $forceFormat = null)
    {
        $this->xmlRootName = [
            'rootElementName' => "nvl-slim-api",
            '_attributes' => [ 'version' => "1.0" ]
        ];
        $mediaType = ($forceFormat) ?? $this->determineMediaType($request);

        // add debug information from middleware
        $meta = $request->getAttribute('meta');
        $acceptHeader = $request->getHeaderLine('Accept');
        $meta['debug']['Accept']= $acceptHeader;
        $meta['debug']['status']= $status;

        // merge meta from both data and middleware
        $data['meta'] = array_merge($data['meta']??[],$meta??[]);

        switch ($mediaType) {
            case APIController::API_XML:
            case 'text/xml':
                $xml = ArrayToXml::convert($data,$this->xmlRootName);
                $body = new Body(fopen('php://temp', 'r+'));
                $body->write($xml);
                return $response->withBody($body)
                    ->withStatus($status)
                    ->withHeader('Content-Type', APIController::API_XML.';charset=utf-8');

            case APIController::API_JSON:
                return $response->withJson($data, $status);

            default:
                throw new \Exception("Unknown media type $mediaType");
        }
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param string   $title
     * @param string   $details
     * @param int       $status
     * @return Response
     * @throws \Exception
     * 
     * @todo[vanch3d] Too restrictive. check Crell/ApiProblem
     */
    protected function renderError(Request $request, Response $response, string $title, string $details, $status=500)
    {
        $err = [
            'status'    => $status,
            'title'     => $title,
            'details'   => $details
        ];
        $ret= [
            'errors' => [$err]
        ];

        return $this->render($request,$response,$ret,$status);
    }

    public function apiHome(Request $request, Response $response)
    {
        return $this->getView()->render($response, 'api/swagger.ui.twig');
    }

    public function getSwagger(Request $request, Response $response)
    {
        $json = json_decode(@file_get_contents(DIR . 'openapi.json'),true);
        //Debugger::barDump($json); die();
        return $this->render($request,$response,$json,200,self::API_JSON);
    }

    private function getUnAPIFormats()
    {
        $formatsList = [
            array(
                "_attributes" => array(
                    'name' => 'rdf_bibliontology',
                    'type' => 'application/xml',
                    'docs' => 'http://bibliontology.com/'
                ),
                "_value" => ""
            ),
            array(
                "_attributes" => array(
                    'name' => 'bibtex',
                    'type' => 'text/plain',
                    'docs' => 'http://www.bibtex.org/'
                ),
                "_value" => ""
            ),
        ];

        return [ "format"=> $formatsList ];
    }

    private function formatUnAPIResponse(Response $response, array $data, array $root = null) : Response
    {
        $xml = ArrayToXml::convert($data,$root ?? $this->unapiRootName);

        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($xml);
        return $response->withBody($body)
            ->withHeader('Content-Type', APIController::API_XML.';charset=utf-8');
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     * @se https://web.archive.org/web/20141218062835/http://unapi.info/
     */
    public function unAPI(Request $request, Response $response)
    {
        //$mediatype = $this->determineMediaType($request);

        $id = $request->getParam("id");
        $format = $request->getParam("format");

        $formats = $this->getUnAPIFormats();

        if (!isset($id) && !isset($format))
        {
            // return the list of supported metadata formats
            $response = $this->formatUnAPIResponse($response,$formats)
                ->withStatus(200);
            return $response;
        }
        else if (!isset($format))
        {
            // return the list of supported metadata formats for the given uri
            $response = $this->formatUnAPIResponse($response,$formats,[
                'rootElementName' => $this->unapiRootName,
                '_attributes' => [ 'id' => $id ]
            ])
                ->withStatus(300);
            return $response;
        }

        if (!isset($id))
        {
            return $this->renderError($request,$response,APIController::ERR_BADREQUEST,
                "the parameter 'id' is missing",404);
        }

        $pub = $this->getPublicationManager()->isDefined($id);
        if (false === $pub)
        {
            return $this->renderError($request,$response,APIController::ERR_NOTFOUND,
                'could not find a document with this id',404);
        }

        $accepted = array_filter($this->getUnAPIFormats()['format'], function ($var) use ($format) {
            return ($format == $var['_attributes']['name']);
        });
        $accepted = reset($accepted);

        if (empty($accepted))
        {
            return $this->renderError($request,$response,APIController::ERR_BADREQUEST,
                'this format is not accepted',406);
        }

        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($pub['output'][$format]);
        return $response->withBody($body)
            ->withStatus(200)
            ->withHeader('Content-Type', $accepted['_attributes']['type'].';charset=utf-8');

    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws \Exception
     */
    public function getAllProjects(Request $request, Response $response, array $args)
    {
        try {
            $projects = $this->getProjectManager()->getData(false);
            return $this->render($request,$response,['data' => $projects]);

        } catch (\Exception $e) {
            return $this->renderError($request,$response,APIController::ERR_INTERNALERROR,
                'could not access the list of projects',500);
        }
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws \Exception
     */
    public function getAllPublications(Request $request, Response $response, array $args)
    {
        $res = $this->getPublicationManager()->getData("all",100);
        $pubs = $res['publications'];
        unset($res['publications']);
        return $this->render($request,$response,[ 'data' => $pubs,'meta'=>$res]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @throws \Exception
     * @return Response
     */
    public function getProject(Request $request, Response $response, array $args)
    {
        $project = $this->getProjectManager()->isDefined($args["name"]);
        if (false === $project) {
            return $this->renderError($request,$response,APIController::ERR_NOTFOUND,
                'The project does not exist',404);

        }
        return $this->render($request,$response,['data' => $project]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response|static
     * @throws \Exception
     */
    public function getPublications(Request $request, Response $response, array $args)
    {
        $project = $this->getProjectManager()->isDefined($args["name"]);
        if (false === $project) {
            return $this->renderError($request,$response,APIController::ERR_NOTFOUND,
                'The project does not exist',404);
        }

        $res = $this->getPublicationManager()->getData($args["name"]);
        $pubs = $res['publications'];
        unset($res['publications']);
        return $this->render($request,$response,[ 'data' => $pubs,'meta'=>$res]);

    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws \Exception
     */
    public function getSlides(Request $request, Response $response, array $args)
    {
        // set the cache for the request
        $response = $this->getCache()->withEtag($response,'nvl-slim.slideshare.'. $args["name"]);
        $response = $this->getCache()->withExpires($response,'+1 week');

        $project = $this->getProjectManager()->isDefined($args["name"]);
        if (false === $project) {
            return $this->renderError($request,$response,APIController::ERR_NOTFOUND,
                'The project does not exist',404);
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
                'username_for' => $cfg['username'],                // Slides owner
                'detailed' => '1'
            );// build the URL for Slideshare API
            $param = http_build_query($data);
            $url2 = 'https://www.slideshare.net/api/2/get_slideshows_by_user?' . $param;
            $slsReq = Requests::get($url2);
            if (!$slsReq->success)
                throw new \Exception('Cannot access the SlideShare API.', 500);
            $xml = new SimpleXMLElement($slsReq->body);
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
                {
                    $slideshow['type'] = 'slide';
                    $slides[] = $slideshow;
                }
            }
            $json['Count'] = count($slides);
            unset($json['Slideshow']);

            //return $response->withJson([ 'data' => $slides,'meta'=> $json]);
            return $this->render($request,$response,[ 'data' => $slides,'meta'=>$json]);


        } catch (\Exception $e) {
            return $this->renderError($request,$response,APIController::ERR_INTERNALERROR,
                $e->getMessage(),$e->getCode());

        }
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws \Exception
     */
    public function getImages(Request $request, Response $response, array $args)
    {
        // set the cache for the request
        $response = $this->getCache()->withEtag($response,'nvl-slim.gallery.'. $args["name"]);
        $response = $this->getCache()->withExpires($response,'+1 week');

        $project = $this->getProjectManager()->isDefined($args["name"]);
        if (false === $project) {
            return $this->renderError($request,$response,APIController::ERR_NOTFOUND,
                'The project does not exist',404);
        }

        $data = array(
            'format'=>		'json',					// Zotero API key
            'method'=>		'pwg.categories.getList',	// Zotero API key
        );

        // build the URL for galery API
        $param = http_build_query($data);
        $url2 = 'http://gallery.calques3d.org/ws.php?'.$param;

        try {

            $reqGalUser = Requests::get($url2);
            $data = json_decode($reqGalUser->body, true);

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
                //return $response->withJson([ 'data' => []]);
                return $this->render($request,$response,[ 'data' => []]);

            }


            $data = array(
                'format' => 'json', // Zotero API key
                'method' => 'pwg.categories.getImages', // Zotero API key
                'cat_id' => $cat['id']
            );

            // build the URL for Zotero API
            $param = http_build_query($data);
            $url2 = 'http://gallery.calques3d.org/ws.php?' . $param;

            $reqGalImg = Requests::get($url2);
            $data = json_decode($reqGalImg->body, true);

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
            //return $response->withJson([ 'data' => $imgs]);
            return $this->render($request,$response,[ 'data' => $imgs]);

        }
        catch (\Exception $e)
        {
            return $this->renderError($request,$response,APIController::ERR_INTERNALERROR,
                $e->getMessage(),$e->getCode());
        }
    }

}