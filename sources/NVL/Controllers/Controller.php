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
use Tracy\Debugger;

abstract class Controller
{
    private $prjManager;
    private $pubManager;

    private $c;
    private $view;
    private $session;
    private $cache;
    private $logger;
    private $settings;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {

        $this->prjManager = new ProjectManager($container);
        $this->pubManager = new ZoteroManager($container);

        $this->c = $container;
        $this->view = $container->get("view");
        $this->session = $container->get("session");
        $this->cache = $container->get("cache");
        $this->logger = $container->get("logger");
        $this->settings = $container->get("settings");
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

    /**
     * @return mixed|settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return \Slim\HttpCache\CacheProvider
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->c;
    }


}