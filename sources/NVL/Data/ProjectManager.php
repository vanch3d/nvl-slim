<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 12:51
 */

namespace NVL\Data;

use Couchbase\ViewQueryEncodable;
use Exception;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Container;
use Slim\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;
use Slim\Views\Twig;


class ProjectManager
{
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Retrieve a project descriptors array based on twig templates in the projects/content directory
     * @param bool $short   false (default) to retrieve the whole description, true
     *                      to extract only the basic identifiers (id, name, url, start date)
     * @return array        An array containing the projects' description
     * @throws Exception
     * @todo                caching (see PHPSocialNetwork/phpFastCache)
     * @todo                project name needs to be redefined within each twig template
     */
    public function getData($short = false)
    {
        $json = array();

        try {
             /** @var Twig $view */
             $view = $this->container->get("view");
             $path = $view->getLoader()->getPaths()[0];

            foreach(glob($path . '/projects/content/*.twig') as $file) {

                $projid = basename($file,".twig");
                $renderedTemplate = $view->fetch("/projects/content/".basename($file), array(
                    'tmpl_base' => 'template.json.twig',
                    'project' => array(
                        "id" => $projid,
                        "name"=>$projid)
                ));
                $data = json_decode($renderedTemplate,true);
                $data['type']='project';
                if ($short)
            {
                    $filter = array("id","type","name","url","start");
                    $data = array_intersect_key($data, array_flip($filter));
                }
                $json[$projid] = $data;
            }
            // sort projects by reverse chronological order (based on start date)
            usort($json,function($a,$b){
                return strcmp($b["start"], $a["start"]);
            });

        } catch (NotFoundExceptionInterface $e) {
            // @todo[vanch3d] Better create our own exceptions
            throw new Exception("");
        } catch (ContainerExceptionInterface $e) {
            throw new Exception("");
        }

        return $json;
    }

    /**
     *
     * @param string $id  The id of the project to check
     * @return mixed        False if the project does not exist, its descriptor if it does
     */
    public function isDefined($id)
    {
        if (empty($id)) return false;
        try {
            $projIdx = $this->getData();
            $found = current(array_filter($projIdx, function($item) use($id) {
                return isset($item['id']) && $id == $item['id'];
            }));
            return $found;

        } catch (Exception $e) {
            return false;
        }
    }

}