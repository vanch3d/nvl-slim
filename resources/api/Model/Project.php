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
 *     description="Project model",
 *     type="object",
 * )
 */
class Project
{
    /**
     * @OAS\Property(
     *     description="The unique identifier of the project"
     * )
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
     * @OAS\Property(
     *     description="The short name of the project"
     * )
     * @var string
     */
    public $name;

    /**
     * @OAS\Property(
     *     description="The long name of the project"
     * )
     * @var string
     */
    public $title;

    /**
     * @OAS\Property(
     *     description="The research institution where the project takes place"
     * )
     * @var string
     */
    public $institution;

    /**
     * @OAS\Property(
     *     description="The place where the institution is based"
     * )
     * @var string
     */
    public $location;

    /**
     * @OAS\Property(
     *     description="The latitude of the research institution (for geolocation)",
     *     type="string",
     *     format="float"
     * )
     * @var string
     */
    public $lat;

    /**
     * @OAS\Property(
     *     description="The longitude of the research institution (for geolocation)",
     *     type="string",
     *     format="float"
     * )
     * @var string
     */
    public $long;

    /**
     * @OAS\Property(
     *     description="The starting date of the project",
     *     type="string",
     *     format="date"
     * )
     * @var string
     */
    public $start;

    /**
     * @OAS\Property(
     *     description="The end date of the project",
     *     type="string",
     *     format="date"
     * )
     * @var string
     */
    public $end;

    /**
     * @OAS\Property(
     *     description="The long description of the project"
     * )
     * @var string
     */
    public $description;

    /**
     * @OAS\Property(
     *     description="A list of LinkedIn URLs of the people involved in the project",
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
     * @OAS\Property(
     *     description="The API endpoint to retrieve the images associated with the project"
     * )
     * @var string
     */
    public $images;

    /**
     * @OAS\Property(
     *     description="The URL of this project"
     * )
     * @var string
     */
    public $url;


}