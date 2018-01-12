<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 12:51
 */

namespace NVL\Data;

use Interop\Container\ContainerInterface;
use Slim\Views\Twig;


class ProjectManager
{
    protected $view = null;

    public function __construct(ContainerInterface $container)
    {
    }

    /**
     * Retrieve a project descriptors array based on twig templates in the projects/content directory
     * @param bool $short   false (default) to retrieve the whole description, true
     *                      to extract only the basic identifiers (id, name, url, start date)
     * @return array        An array containing the projects' description
     * @throws \Exception
     * @todo                caching (see PHPSocialNetwork/phpFastCache)
     * @todo                project name needs to be redefined within each twig template
     */
    public function getData($short = false)
    {
        $json = array();

        throw new \Exception("TODO");

        return $json;
    }

}