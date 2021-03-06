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
use Slim\Router;
use Slim\Views\Twig;
use Tracy\Debugger;

abstract class Controller
{
    private $prjManager;
    private $pubManager;

    private $c;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {

        $this->prjManager = new ProjectManager($container);
        $this->pubManager = new ZoteroManager($container);

        $this->c = $container;
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
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->c;
    }


    /**
     * @return Twig
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getView()
    {
        return $this->c->get("view");
    }

    /**
     * @return Session
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getSession() : Session
    {
        return $this->c->get("session");
    }

    /**
     * @return Logger
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getLogger() : Logger
    {
        return $this->c->get("logger");
    }

    /**
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getSettings()
    {
        return $this->c->get("settings");
    }

    /**
     * @return \Slim\HttpCache\CacheProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getCache()
    {
        return $this->c->get("cache");
    }

    /**
     * @return Router
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getRouter()
    {
        return $this->c->get("routerXXXX");
    }

    public function notFound(Request $request, Response $response, \Exception $e)
    {
        $notFoundHandler = $this->c->get('notFoundHandler');
        return $notFoundHandler($request->withAttribute('message', $e->getMessage()), $response);
    }

}