<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 26/01/2018
 * Time: 14:37
 */

namespace NVL;

use DOMDocument;
use Slim\Http\Response;
use Slim\Router;

class APIControllerTest extends SlimApp_TestCase
{
    const unapiFormats = "<formats><format name=\"rdf_bibliontology\" type=\"application/xml\" docs=\"http://bibliontology.com/\"></format><format name=\"bibtex\" type=\"text/plain\" docs=\"http://www.bibtex.org/\"></format></formats>";
    const unapiIdTrue = "2016.IMS.myPAL";
    const unapiIdFalse = "2017.IMS.myPAL";
    const unapiFormatTrue = "bibtex";
    const unapiFormatFalse = "not accepted format";



    private function getExpectedUnapiFormats(string $id=null) : string
    {
        if (isset($id))
        {
            return str_replace("<formats>","<formats id=\"$id\">",self::unapiFormats);

        }
        return self::unapiFormats;
    }

    /**
     * The App object has been initialised
     */
    public function testAppInitialised()
    {
        $this->assertTrue($this->app !== null,"Slim App has not been initialised");
    }

    /**
     * A wrong route name throws an exception
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testUnAPIWithWrongRouteNAme()
    {
        $this->expectException(\RuntimeException::class);

        /** @var Router $r */
        $r = $this->app->getContainer()->get("router");
        $p = $r->pathFor('api.unapi.wrongname');

        $request = $this->getRequest("get",$p);
        $response = $this->app->process($request, new Response());
    }

    /**
     * Call to unapi without parameters returns 200 and a XML 'formats' object
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testUnAPIWithNoParam()
    {
        /** @var Router $router */
        $router = $this->app->getContainer()->get("router");
        $path = $router->pathFor('api.unapi');
        $request = $this->getRequest("get",$path);
        $response = $this->app->process($request, new Response());

        // check status
        $this->assertEquals(200,$response->getStatusCode());

        $contentType = $response->getHeader('Content-Type');
        $contentType = reset($contentType);

        // check content type
        $this->assertRegExp('/xml/',$contentType,"The Content-Type of unapi should be xml");

        $xml = (string)$response->getBody();

        // check content
        $this->assertXmlStringEqualsXmlString($this->getExpectedUnapiFormats(),$xml);
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testUnAPIWithIdParam()
    {
        /** @var Router $router */
        $router = $this->app->getContainer()->get("router");
        $path = $router->pathFor('api.unapi');
        $request = $this->getRequest("get",$path,["id" => self::unapiIdTrue] );
        $response = $this->app->process($request, new Response());

        // check status
        $this->assertEquals(300,$response->getStatusCode());

        $contentType = $response->getHeader('Content-Type');
        $contentType = reset($contentType);

        // check content type
        $this->assertRegExp('/xml/',$contentType,"The Content-Type of unapi should be xml");

        $xml = (string)$response->getBody();

        // check content
        $this->assertXmlStringEqualsXmlString($this->getExpectedUnapiFormats(self::unapiIdTrue),$xml);
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testUnAPIWithFormatParam()
    {
        /** @var Router $router */
        $router = $this->app->getContainer()->get("router");
        $path = $router->pathFor('api.unapi');
        $request = $this->getRequest("get",$path,["format" => self::unapiFormatTrue] );
        $response = $this->app->process($request, new Response());

        // check status
        $this->assertEquals(404,$response->getStatusCode());

        $contentType = $response->getHeader('Content-Type');
        $contentType = reset($contentType);

        // check content type
        $this->assertRegExp('/xml/',$contentType,"The Content-Type of unapi should be xml");

        $xml = (string)$response->getBody();
        $xmlParser = xml_parser_create();
        xml_parse_into_struct($xmlParser, $xml, $structure, $index);
        xml_parser_free($xmlParser);

        // check content (partially)
        // @todo[vanch3d] find a better way of checking content when only certain elements are required (eg ERRORS)
        $this->assertEquals(strtoupper("nvl-slim-api"),$structure[0]['tag']);
        $this->assertEquals(strtoupper("1.0"),$structure[0]['attributes']['VERSION']);
        $this->assertEquals(strtoupper("ERRORS"),$structure[1]['tag']);
        $this->assertEquals(strtoupper("2"),$structure[1]['level']);

    }


    public function testUnAPIWithBothParamsWrongId()
    {
        /** @var Router $router */
        $router = $this->app->getContainer()->get("router");
        $path = $router->pathFor('api.unapi');
        $request = $this->getRequest("get",$path,["format" => self::unapiFormatFalse,"id" => self::unapiIdFalse] );
        $response = $this->app->process($request, new Response());

        // check status
        $this->assertEquals(404,$response->getStatusCode());

        $contentType = $response->getHeader('Content-Type');
        $contentType = reset($contentType);

        // check content type
        $this->assertRegExp('/xml/',$contentType,"The Content-Type of unapi should be xml");

        $xml = (string)$response->getBody();
        $xmlParser = xml_parser_create();
        xml_parse_into_struct($xmlParser, $xml, $structure, $index);
        xml_parser_free($xmlParser);

        // check content (partially)
        // @todo[vanch3d] find a better way of checking content when only certain elements are required (eg ERRORS)
        $this->assertEquals(strtoupper("nvl-slim-api"),$structure[0]['tag']);
        $this->assertEquals(("1.0"),$structure[0]['attributes']['VERSION']);
        $this->assertEquals(strtoupper("ERRORS"),$structure[1]['tag']);
        $this->assertEquals(("2"),$structure[1]['level']);
        $this->assertEquals(strtoupper("TITLE"),$structure[3]['tag']);
        $this->assertEquals("not found",$structure[3]['value']);

    }


    public function testUnAPIWithBothParamsWrongFormat()
    {
        /** @var Router $router */
        $router = $this->app->getContainer()->get("router");
        $path = $router->pathFor('api.unapi');
        $request = $this->getRequest("get",$path,["format" => self::unapiFormatFalse,"id" => self::unapiIdTrue] );
        $response = $this->app->process($request, new Response());

        // check status
        $this->assertEquals(406,$response->getStatusCode());

        $contentType = $response->getHeader('Content-Type');
        $contentType = reset($contentType);

        // check content type
        $this->assertRegExp('/xml/',$contentType,"The Content-Type of unapi should be xml");

        $xml = (string)$response->getBody();
        $xmlParser = xml_parser_create();
        xml_parse_into_struct($xmlParser, $xml, $structure, $index);
        xml_parser_free($xmlParser);

        // check content (partially)
        // @todo[vanch3d] find a better way of checking content when only certain elements are required (eg ERRORS)
        $this->assertEquals(strtoupper("nvl-slim-api"),$structure[0]['tag']);
        $this->assertEquals(("1.0"),$structure[0]['attributes']['VERSION']);
        $this->assertEquals(strtoupper("ERRORS"),$structure[1]['tag']);
        $this->assertEquals(("2"),$structure[1]['level']);
        $this->assertEquals(strtoupper("TITLE"),$structure[3]['tag']);
        $this->assertEquals("bad request",$structure[3]['value']);
    }

    public function testUnAPIWithBothCorrectParams()
    {
        /** @var Router $router */
        $router = $this->app->getContainer()->get("router");
        $path = $router->pathFor('api.unapi');
        $request = $this->getRequest("get",$path,["format" => self::unapiFormatTrue,"id" => self::unapiIdTrue] );
        $response = $this->app->process($request, new Response());

        // check status
        $this->assertEquals(200,$response->getStatusCode());

        $contentType = $response->getHeader('Content-Type');
        $contentType = reset($contentType);

        // check content type
        $this->assertRegExp('/plain/',$contentType,"The Content-Type of unapi should be text/plain");

        $xml = (string)$response->getBody();
        var_dump($xml);
        $this->assertContains("@inproceedings{van_labeke_personalised_2016",$xml);
    }

}
