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
 *     description="GitHub Contributors",
 *     type="object",
 * )
 */
class Author
{
    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $login;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var integer
     */
    public $id;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $avatar_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $gravatar_id;
    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $html_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $followers_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $following_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $gists_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $starred_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $subscriptions_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $organizations_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $repos_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $events_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $received_events_url;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var string
     */
    public $type;

    /**
     * @OAS\Property(
     *     description="",
     * )
     * @var boolean
     */
    public $site_admin;
}