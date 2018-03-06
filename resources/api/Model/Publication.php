<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 14:15
 */

namespace NVL;

use Swagger\Annotations as OAS;

/**
 * @OAS\Schema(
 *     type="object",
 *     description="Publication Model"
 * )
 */
class Publication
{
    /**
     * @OAS\Property()
     * @var string
     */
    public $id;

    /**
     * @OAS\Property(
     *     enum={"paper-conference","thesis"}
     * )
     * @var string
     */
    public $type;

    /**
     * @OAS\Property()
     * @var string
     */
    public $title;

    /**
     * @OAS\Property(
     *     property="container-title"
     * )
     * @var string
     */
    public $container_title;

    /**
     * @OAS\Property(
     *     property="publisher-place"
     * )
     * @var string
     */
    public $publisher_place;

    /**
     * @OAS\Property()
     * @var string
     */
    public $page;

    /**
     * @OAS\Property(
     *     property="archive-location"
     * )
     * @var string
     */
    public $archive_location;

    /**
     * @OAS\Property()
     * @var string
     */
    public $event;

    /**
     * @OAS\Property(
     *     property="event-place"
     * )
     * @var string
     */
    public $event_place;

    /**
     * @OAS\Property()
     * @var string
     */
    public $abstract;

    /**
     * @OAS\Property(
     *     property="URL"
     * )
     * @var string
     */
    public $URL;

    /**
     * @OAS\Property(
     *     property="DOI"
     * )
     * @var string
     */
    public $DOI;

    /**
     * @OAS\Property(
     *     property="PDF",
     *     type="string",
     *     format="url"
     * )
     * @var string
     */
    public $PDF;

    /**
     * @@todo[vanch3d] Define authors format (e.g. People)
     * @OAS\Property(
     *     property="authors",
     *     @OAS\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $authors;

    /**
     * @todo[vanch3d] Define issued/date-parts structure
     * @OAS\Property(
     *     property="issued",
     *     @OAS\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $issued;

    /**
     * @todo[vanch3d] Define structured list of available output
     *
     * @OAS\Property(
     *     @OAS\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $output;

    /**
     * @todo[vanch3d] Define project (id/url)
     *
     * @OAS\Property(
     *     @OAS\Items(
     *          type="string"
     *      )
     * )
     * @var array
     */
    public $project;
}