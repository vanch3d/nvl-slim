<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 12:17
 */

namespace NVL;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"id","name","type"},
 *     @SWG\Xml(
 *          name="Project"
 *     )
 * )
 */
class Project
{
    /**
     * @SWG\Property()
     * @var string
     */
    public $id;

    /**
     * @SWG\Property(
     *     default="project",
     *     enum={"project"}
     * )
     * @var string
     */
    public $type;


    /**
     * @SWG\Property()
     * @var string
     */
    public $name;

    /**
     * @SWG\Property()
     * @var string
     */
    public $title;

    /**
     * @SWG\Property()
     * @var string
     */
    public $institution;

    /**
     * @SWG\Property()
     * @var string
     */
    public $location;

    /**
     * @SWG\Property()
     * @var string
     */
    public $lat;

    /**
     * @SWG\Property()
     * @var string
     */
    public $long;

    /**
     * @SWG\Property(
     *     type="string",
     *     format="date"
     * )
     * @var string
     */
    public $start;

    /**
     * @SWG\Property(
     *     type="string",
     *     format="date"
     * )
     * @var string
     */
    public $end;

    /**
     * @SWG\Property()
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(
     *          type="string",
     *          format="url"
     *      )
     * )
     * @var array
     */
    public $people;

    /**
     * @SWG\Property(
     *     type="array",
     *     description="A list of github repositories, described by the string '{user}/{repo}'",
     *     @SWG\Items(
     *          type="string",
     *          format="user/repository"
     *      )
     * )
     * @var array
     */
    public $github;

    /**
     * @SWG\Property()
     * @var string
     */
    public $images;

    /**
     * @SWG\Property()
     * @var string
     */
    public $url;


}