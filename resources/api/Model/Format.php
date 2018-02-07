<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 06/02/2018
 * Time: 11:16
 */

namespace NVL;
use Swagger\Annotations as OAS;


/**
 * @OAS\Schema(
 *     type="object",
 *     @OAS\Xml(
 *          name="formats"
 *      )
 * )
 */
class PublicationFormats
{
    /**
     * @OAS\Property(
     *     @OAS\Xml(
     *          attribute=true,
     *          name="id"
     *      )
     * )
     * @var string
     */
    public $id;

    /**
     * @OAS\Property(
     *     type="array",
     *     @OAS\Items(
     *          ref="#/components/schemas/Format"
     *     ),
     *     example={
     *          { "name":"rdf_bibliontology", "docs":"http://bibliontology.com/", "type":"application/xml"},
     *          { "name":"bibtex", "docs":"http://www.bibtex.org/","type":"text/plain"}
     *     },
     *     @OAS\Xml(
     *          name="format"
     *     ),
     * )
     * @var array
     */
    public $formats;
}

/**
 * @OAS\Schema(
 *     type="object",
 *     @OAS\Xml(
 *          name="formats"
 *      )
 * )
 */
class AllFormats
{
    /**
     * @OAS\Property(
     *     type="array",
     *     @OAS\Items(
     *          ref="#/components/schemas/Format"
     *     ),
     *     example={
     *          { "name":"rdf_bibliontology", "docs":"http://bibliontology.com/", "type":"application/xml"},
     *          { "name":"bibtex", "docs":"http://www.bibtex.org/","type":"text/plain"}
     *     },
     *     @OAS\Xml(
     *          name="format"
     *     ),
     * )
     * @var array
     */
    public $formats;

}


/**
 * @OAS\Schema(
 *     title="UNAPI Format model",
 *     type="object",
 * )
 */
class Format
{
    /**
     * @OAS\Property(
     *      x={"example"="rdf_bibliontology"},
     *      @OAS\Xml(
     *          attribute=true,
     *          name="name"
     *      )
     * )
     * @var string
     */
    public $name;
    /**
     * @OAS\Property(
     *      x={"example" : "application/xml"},
     *      @OAS\Xml(
     *          attribute=true,
     *          name="type"
     *      )
     * )
     * @var string
     */
    public $type;

    /**
     * @OAS\Property(
     *      x={"example":"http://bibliontology.com/"},
     *      @OAS\Xml(
     *          attribute=true,
     *          name="docs"
     *      )
     * )
     * @var string
     */
    public $docs;

}