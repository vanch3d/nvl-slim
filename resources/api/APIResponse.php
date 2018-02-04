<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 14:34
 */

use Swagger\Annotations as SWG;

/**
 * @SWG\Response(
 *      response="JSON",
 *      description="The basic json response",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              property="data",
 *              type="object"
 *          ),
 *          @SWG\Property(
 *              property="errors",
 *              type="object"
 *          ),
 *          @SWG\Property(
 *              property="meta",
 *              type="object"
 *          )
 *      )
 * )
 *
 */