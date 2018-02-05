<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 12:17
 */

namespace NVL;

use Swagger\Annotations as OAS;

/**
 * @OAS\Schema(
 *     title="Project model",
 *     type="object",
 * )
 */
class Project
{
    /**
     * @OAS\Property()
     * @var string
     */
    public $id;

    /**
     * @OAS\Property(
     *     default="project",
     *     enum={"project"}
     * )
     * @var string
     */
    public $type;


    /**
     * @OAS\Property()
     * @var string
     */
    public $name;

    /**
     * @OAS\Property()
     * @var string
     */
    public $title;

    /**
     * @OAS\Property()
     * @var string
     */
    public $institution;

    /**
     * @OAS\Property()
     * @var string
     */
    public $location;

    /**
     * @OAS\Property()
     * @var string
     */
    public $lat;

    /**
     * @OAS\Property()
     * @var string
     */
    public $long;

    /**
     * @OAS\Property(
     *     type="string",
     *     format="date"
     * )
     * @var string
     */
    public $start;

    /**
     * @OAS\Property(
     *     type="string",
     *     format="date"
     * )
     * @var string
     */
    public $end;

    /**
     * @OAS\Property()
     * @var string
     */
    public $description;

    /**
     * @OAS\Property(
     *     type="array",
     *     @OAS\Items(
     *          type="string",
     *          format="url"
     *      )
     * )
     * @var array
     */
    public $people;

    /**
     * @OAS\Property(
     *     type="array",
     *     description="A list of github repositories, described by the string '{user}/{repo}'",
     *     @OAS\Items(
     *          type="string",
     *          format="user/repository"
     *      )
     * )
     * @var array
     */
    public $github;

    /**
     * @OAS\Property()
     * @var string
     */
    public $images;

    /**
     * @OAS\Property()
     * @var string
     */
    public $url;


}