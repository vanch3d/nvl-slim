<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 05/02/2018
 * Time: 00:01
 */

namespace NVL;

use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"ID","type"},
 *     @SWG\Xml(
 *          name="Slide"
 *     )
 * )
 */
class Slide
{
    /**
     * @SWG\Property(
     *     enum={"slide"},
     *     default="slide"
     * )
     * @var string
     */
    public $type;

    /**
     * @SWG\Property()
     * @var string
     */
    public $ID;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Title;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Description;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Status;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Username;

    /**
     * @SWG\Property(
     *     format="url"
     * )
     * @var string
     */

    public $URL;
    /**
     * @SWG\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailURL;
    /**
     * @SWG\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailSmallURL;

    /**
     * @SWG\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailXLargeURL;

    /**
     * @SWG\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailXXLargeURL;

    /**
     * @SWG\Property()
     * @var string
     */
    public $ThumbnailSize;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Embed;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Created;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Updated;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Language;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Format;

    /**
     * @SWG\Property()
     * @var string
     */
    public $Download;

    /**
     * @SWG\Property()
     * @var string
     */
    public $DownloadUrl;

    /**
     * @SWG\Property()
     * @var string
     */
    public $SecretKey;

    /**
     * @SWG\Property()
     * @var string
     */
    public $SlideshowEmbedUrl;

    /**
     * @SWG\Property()
     * @var string
     */
    public $SlideshowType;

    /**
     * @SWG\Property()
     * @var string
     */
    public $UserID;
    /**
     * @SWG\Property()
     * @var string
     */
    public $PPTLocation;
    /**
     * @SWG\Property()
     * @var string
     */
    public $StrippedTitle;
    /**
     * @SWG\Property()
     * @var string
     */
    public $Tags;
    /**
     * @SWG\Property()
     * @var string
     */
    public $NumDownloads;
    /**
     * @SWG\Property()
     * @var string
     */
    public $NumViews;
    /**
     * @SWG\Property()
     * @var string
     */
    public $NumComments;
    /**
     * @SWG\Property()
     * @var string
     */
    public $NumFavorites;
    /**
     * @SWG\Property()
     * @var string
     */
    public $NumSlides;
    /**
     * @SWG\Property()
     * @var string
     */
    public $RelatedSlideshows;
    /**
     * @SWG\Property()
     * @var string
     */
    public $PrivacyLevel;
    /**
     * @SWG\Property()
     * @var string
     */
    public $FlagVisible;
    /**
     * @SWG\Property()
     * @var string
     */
    public $ShowOnSS;
    /**
     * @SWG\Property()
     * @var string
     */
    public $SecretURL;
    /**
     * @SWG\Property()
     * @var string
     */
    public $AllowEmbed;
    /**
     * @SWG\Property()
     * @var string
     */
    public $ShareWithContacts;
 }