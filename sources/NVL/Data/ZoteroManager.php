<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 12:49
 */

namespace NVL\Data;

use Interop\Container\ContainerInterface;

/**
 * Class ZoteroManager
 * @package NVL\Data
 *
 * @todo[vanch3d] Create an interface in case of different publication manager (eg Mendeley)
 */
class ZoteroManager
{
    protected $view = null;

    public function __construct(ContainerInterface $container)
    {
    }

    /**
     * @param string $project_id    The id of the project to retrieve publications from (or 'all')
     * @param int $limit            The maximum number of publications to retrieve
     * @param string $csl_file      The CSL file to use for formatting the publications
     * @return array                A list of publications
     * @throws \Exception
     *
     * @todo[vanch3d] Make sure all URLs are fully qualified (eg PDF, URL, ...)
     */
    public function getData($project_id="all", $limit = 20, $csl_file="umuai-nvl.csl")
    {
        // storage for publications data
        $pubs = array();

        throw new \Exception("TODO");

        return $pubs;
    }

}