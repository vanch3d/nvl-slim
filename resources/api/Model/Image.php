<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 05/02/2018
 * Time: 00:10
 */

namespace NVL;

use Swagger\Annotations as OAS;

/**
 * @OAS\Schema(
 *     title="Image model",
 *     type="object",
 * )
 */
class Image
{
    /**
     * @OAS\Property()
     * @var string
     */
    public $id;

    /**
     * @OAS\Property(
     *     enum={"image"},
     *     default="image"
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
     * @OAS\Property()
     * @var string
     */
    public $comment;

    /**
     * @OAS\Property(
     *     format="url"
     * )
     * @var string
     */
    public $url;

    /**
     * @OAS\Property(
     *     format="url"
     * )
     * @var string
     */
    public $thumb;

}