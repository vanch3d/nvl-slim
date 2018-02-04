<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 14:15
 */

namespace NVL;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"id","type","archive-location"},
 *     @SWG\Xml(
 *          name="Publication"
 *     )
 * )
 */
class Publication
{
    /**
     * @SWG\Property()
     * @var string
     */
    public $id;

    /**
     * @SWG\Property(
     *     enum={"paper-conference","thesis"}
     * )
     * @var string
     */
    public $type;

    /**
     * @SWG\Property()
     * @var string
     */
    public $title;

    /**
     * @SWG\Property(
     *     property="container-title"
     * )
     * @var string
     */
    public $container_title;

    /**
     * @SWG\Property(
     *     property="publisher-place"
     * )
     * @var string
     */
    public $publisher_place;

    /**
     * @SWG\Property()
     * @var string
     */
    public $page;

    /**
     * @SWG\Property(
     *     property="archive-location"
     * )
     * @var string
     */
    public $archive_location;

    /**
     * @SWG\Property()
     * @var string
     */
    public $event;

    /**
     * @SWG\Property(
     *     property="event-place"
     * )
     * @var string
     */
    public $event_place;

    /**
     * @SWG\Property()
     * @var string
     */
    public $abstract;

    /**
     * @SWG\Property(
     *     property="URL"
     * )
     * @var string
     */
    public $URL;

    /**
     * @SWG\Property(
     *     property="DOI"
     * )
     * @var string
     */
    public $DOI;

    /**
     * @SWG\Property(
     *     property="PDF",
     *     type="string",
     *     format="url"
     * )
     * @var string
     */
    public $PDF;

    /**
     * @@todo[vanch3d] Define authors format (e.g. People)
     * @SWG\Property(
     *     property="authors",
     *     @SWG\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $authors;

    /**
     * @todo[vanch3d] Define issued/date-parts structure
     * @SWG\Property(
     *     property="issued",
     *     @SWG\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $issued;

    /**
     * @todo[vanch3d] Define structured list of available output
     *
     * @SWG\Property(
     *     @SWG\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $output;

    /**
     * @todo[vanch3d] Define project (id/url)
     *
     * @SWG\Property(
     *     @SWG\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $project;
}