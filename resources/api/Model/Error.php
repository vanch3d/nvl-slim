<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 07/02/2018
 * Time: 16:08
 */

namespace NVL;
use Swagger\Annotations as OAS;

/**
 * @OAS\Schema(
 *     type="object",
 *     @OAS\Xml(
 *          name="nvl-slim-api"
 *      )
 * )
 */
class NVLSlimAPI
{
    /**
     * @OAS\Property(
     *     @OAS\Xml(
     *          attribute=true,
     *          name="version"
     *      ),
     *     example="0.3"
     * )
     * @var string
     */
    public $version;

    /**
     * @OAS\Property(
     *     description="An array of errors",
     *     @OAS\Items(
     *          ref="#/components/schemas/Error"
     *     )
     * )
     * @var array
     */
    public $error;
}

/**
 * @OAS\Schema(
 *     type="object",
 *     @OAS\Xml(
 *          name="Error"
 *      )
 * )
 */
class Error
{

    /**
     * @OAS\Property(
     *     @OAS\Xml(
     *          attribute=false,
     *          name="status"
     *      )
     * )
     * @var integer
     */
    public $status;

    /**
     * @OAS\Property(
     *     @OAS\Xml(
     *          attribute=false,
     *          name="titlw"
     *      )
     * )
     * @var string
     */
    public $title;

    /**
     * @OAS\Property(
     *     @OAS\Xml(
     *          attribute=false,
     *          name="details"
     *      )
     * )
     * @var string
     */
    public $details;
}