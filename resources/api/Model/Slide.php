<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 05/02/2018
 * Time: 00:01
 */

namespace NVL;

use Swagger\Annotations as OAS;

/**
 * @OAS\Schema(
 *     type="object",
 *     description="From SlideShare API, a complete copy of the response body.",
 *     @OAS\ExternalDocumentation(
 *          description="SlideShare API",
 *          url="https://www.slideshare.net/developers/documentation#get_slideshows_by_user"
 *     )
 * )
 */
class Slide
{
    /**
     * @OAS\Property(
     *     enum={"slide"},
     *     default="slide"
     * )
     * @var string
     */
    public $type;

    /**
     * @OAS\Property()
     * @var string
     */
    public $ID;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Title;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Description;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Status;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Username;

    /**
     * @OAS\Property(
     *     format="url"
     * )
     * @var string
     */

    public $URL;
    /**
     * @OAS\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailURL;
    /**
     * @OAS\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailSmallURL;

    /**
     * @OAS\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailXLargeURL;

    /**
     * @OAS\Property(
     *     format="url"
     * )
     * @var string
     */
    public $ThumbnailXXLargeURL;

    /**
     * @OAS\Property()
     * @var string
     */
    public $ThumbnailSize;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Embed;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Created;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Updated;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Language;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Format;

    /**
     * @OAS\Property()
     * @var string
     */
    public $Download;

    /**
     * @OAS\Property()
     * @var string
     */
    public $DownloadUrl;

    /**
     * @OAS\Property()
     * @var string
     */
    public $SecretKey;

    /**
     * @OAS\Property()
     * @var string
     */
    public $SlideshowEmbedUrl;

    /**
     * @OAS\Property()
     * @var string
     */
    public $SlideshowType;

    /**
     * @OAS\Property()
     * @var string
     */
    public $UserID;
    /**
     * @OAS\Property()
     * @var string
     */
    public $PPTLocation;
    /**
     * @OAS\Property()
     * @var string
     */
    public $StrippedTitle;
    /**
     * @OAS\Property()
     * @var string
     */
    public $Tags;
    /**
     * @OAS\Property()
     * @var string
     */
    public $NumDownloads;
    /**
     * @OAS\Property()
     * @var string
     */
    public $NumViews;
    /**
     * @OAS\Property()
     * @var string
     */
    public $NumComments;
    /**
     * @OAS\Property()
     * @var string
     */
    public $NumFavorites;
    /**
     * @OAS\Property()
     * @var string
     */
    public $NumSlides;
    /**
     * @OAS\Property()
     * @var string
     */
    public $RelatedSlideshows;
    /**
     * @OAS\Property()
     * @var string
     */
    public $PrivacyLevel;
    /**
     * @OAS\Property()
     * @var string
     */
    public $FlagVisible;
    /**
     * @OAS\Property()
     * @var string
     */
    public $ShowOnSS;
    /**
     * @OAS\Property()
     * @var string
     */
    public $SecretURL;
    /**
     * @OAS\Property()
     * @var string
     */
    public $AllowEmbed;
    /**
     * @OAS\Property()
     * @var string
     */
    public $ShareWithContacts;
 }