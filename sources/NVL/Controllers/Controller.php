<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:35
 */

namespace NVL\Controllers;

use Interop\Container\ContainerInterface;
use Monolog\Logger;
use NVL\Data\ProjectManager;
use NVL\Data\ZoteroManager;
use NVL\Support\Storage\Session;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

abstract class Controller
{
    private $prjManager;
    private $pubManager;

    private $c;
    private $view;
    private $session;
    private $logger;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {

        $this->prjManager = new ProjectManager($container);
        $this->pubManager = new ZoteroManager($container);

        $this->c = $container;
        $this->view = $container->get("view");
        $this->session = $container->get("session");
        $this->logger = $container->get("logger");
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @todo[vanch3d] A temporary 'catch all' route. To be removed when all routes defined
     */
    public function _default(Request $request, Response $response, array $args)
    {
        /** @var Route $req */
        $req = $request->getAttribute("route");
        $pattern = $req->getPattern();
        dump("this is the route for $pattern");
    }


    /**
     * @return Twig
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return ProjectManager
     */
    public function getProjectManager()
    {
        return $this->prjManager;
    }

    /**
     * @return ZoteroManager
     */
    public function getPublicationManager()
    {
        return $this->pubManager;
    }

    /**
     * @return Logger
     */
    public function getLogger() : Logger
    {
        return $this->logger;
    }

    public function notFound(Request $request, Response $response, \Exception $e)
    {
        $notFoundHandler = $this->c->get('notFoundHandler');
        return $notFoundHandler($request->withAttribute('message', $e->getMessage()), $response);
    }


}