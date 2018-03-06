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
 *     description="GitHub Contributions",
 *     type="object",
 * )
 */
class Contribution
{
    /**
     * @OAS\Property(
     *     description="The name of the GitHub repository",
     *     format="{user}/{repo}"
     * )
     * @var string
     */
    public $repos;

    /**
     * @OAS\Property(
     *     description="The URL of the full description of the GitHub repository",
     *     format="url"
     * )
     * @var string
     */
    public $url;

    /**
     * @OAS\Property(
     *     description="A list all contributions on the repository, aggregated per week",
     *     type="array",
     *     @OAS\Items(
     *          type="object",
     *          @OAS\Property(
     *              description="The weekly index (0-based) of the contribution",
     *              property="id",
     *              type="integer"
     *          ),
     *          @OAS\Property(
     *              description="The unix timestamp of the week of the contributions (based on Sunday as first weekday)",
     *              property="w",
     *              type="integer"
     *          ),
     *          @OAS\Property(
     *              description="The number of additions to the repository",
     *              property="a",
     *              type="integer"
     *          ),
     *          @OAS\Property(
     *              description="The number of commits to the repository",
     *              property="c",
     *              type="integer"
     *          ),
     *          @OAS\Property(
     *              description="The number of deletions to the repository",
     *              property="d",
     *              type="integer"
     *          ),
     *     )
     * )
     * @var array
     */
    public $weeks;
}