<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 05/02/2018
 * Time: 00:10
 */

namespace NVL;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *     type="object",
 *     @SWG\Xml(
 *          name="Image"
 *     )
 * )
 */
class Image
{
    /**
     * @SWG\Property()
     * @var string
     */
    public $id;

    /**
     * @SWG\Property(
     *     enum={"image"},
     *     default="image"
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
     * @SWG\Property()
     * @var string
     */
    public $comment;

    /**
     * @SWG\Property(
     *     format="url"
     * )
     * @var string
     */
    public $url;

    /**
     * @SWG\Property(
     *     format="url"
     * )
     * @var string
     */
    public $thumb;

}