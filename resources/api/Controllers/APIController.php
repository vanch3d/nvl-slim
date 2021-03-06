<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 03/02/2018
 * Time: 18:25
 */

namespace NVL;

use NVL\Controllers\APIController;
use Slim\Http\Request;
use Slim\Http\Response;
use Swagger\Annotations as OAS;

/**
 * Class SwaggerAPI
 * @see https://gist.github.com/vanch3d/fce679e6ab9f6877a27d7a21c5170aa9
 */
class SwaggerAPI extends APIController
{
    /**
     * @OAS\Get(
     *     path="/unapi",
     *     summary="Base of the unAPI service",
     *     tags={"unAPI"},
     *     description="",
     *     operationId="unAPI",
     *     @OAS\Parameter(
     *          name="id",
     *          in="query",
     *          description="the unique id of the publication to be retrieved",
     *          required=true,
     *          @OAS\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OAS\Parameter(
     *          name="format",
     *          in="query",
     *          description="the format of the publication record to be retrieved",
     *          required=true,
     *          @OAS\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="no parameters in query; return the list of all supported formats",
     *          @OAS\MediaType(
     *              mediaType="application/xml",
     *              @OAS\Schema(
     *                  ref="#/components/schemas/AllFormats"
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *          response=404,
     *          description="bad request: the id is missing or not recognised",
     *          @OAS\MediaType(
     *              mediaType="application/xml",
     *              @OAS\Schema(
     *                  type="object",
     *                  @OAS\Xml( name= "nvl-slim-api" ),
     *                  @OAS\Property(
     *                      property="version",
     *                      type="string",
     *                      @OAS\Xml( attribute=true ),
     *                      example="0.3",
     *                  ),
     *                  @OAS\Property(
     *                      property="error",
     *                      type="array",
     *                        ref="#/components/schemas/Error",
     *                  )
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *          response=300,
     *          description="format is missing; return a list of supported formats",
     *          @OAS\MediaType(
     *              mediaType="application/xml",
     *              @OAS\Schema(
     *                  ref="#/components/schemas/PublicationFormats"
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=406,
     *         description="bad request; the format is not recognised",
     *          @OAS\MediaType(
     *              mediaType="application/xml",
     *              @OAS\Schema(
     *                  type="object",
     *                  @OAS\Xml( name= "nvl-slim-api" ),
     *                  @OAS\Property(
     *                      property="version",
     *                      type="string",
     *                      @OAS\Xml( attribute=true ),
     *                      example="0.3",
     *                  ),
     *                  @OAS\Property(
     *                      property="error",
     *                      type="array",
     *                        ref="#/components/schemas/Error",
     *                  )
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=302,
     *         description="operation successful; redirect to the publication record in the given format",
     *          @OAS\MediaType(
     *              mediaType="application/xml",
     *              @OAS\Schema(
     *                  type="object",
     *                  @OAS\Xml( name= "nvl-slim-api" ),
     *                  @OAS\Property(
     *                      property="version",
     *                      type="string",
     *                      @OAS\Xml( attribute=true ),
     *                      example="0.3",
     *                  ),
     *                  @OAS\Property(
     *                      property="error",
     *                      type="array",
     *                        ref="#/components/schemas/Error",
     *                  )
     *              ),
     *          ),
     *     )
     * )
     */
    public function unAPI(Request $request, Response $response)
    {
        return parent::unAPI($request, $response); // TODO: Change the autogenerated stub
    }

    /**
     * @OAS\Get(
     *     path="/projects",
     *     summary="Get the list of all projects in the application",
     *     tags={"Project"},
     *     description="",
     *     operationId="getAllProjects",
     *     @OAS\Parameter(
     *          ref="#/components/parameters/API_output",
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="successful operation",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  @OAS\Property(
     *                      property="data",
     *                      type="array",
     *                      @OAS\Items(
     *                          ref="#/components/schemas/Project"
     *                      ),
     *                  ),
     *                  @OAS\Property(property="metadata",type="object"),
     *                  @OAS\Property(property="errors",type="array"),
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=500,
     *         description="cannot generate the list",
     *     ),
     * )
     */
    public function getAllProjects(Request $request, Response $response, array $args)
    {
        return parent::getAllProjects($request, $response, $args); // TODO: Change the autogenerated stub
    }

    /**
     * @OAS\Get(
     *     path="/publications",
     *     summary="Get the list of all publications",
     *     tags={"Publication"},
     *     description="",
     *     operationId="getAllPublications",
     *     @OAS\Parameter(
     *          ref="#/components/parameters/API_output",
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="successful operation",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  @OAS\Property(
     *                      property="data",
     *                      type="array",
     *                      @OAS\Items(
     *                          ref="#/components/schemas/Publication"
     *                      ),
     *                  ),
     *                  @OAS\Property(property="metadata",type="object"),
     *                  @OAS\Property(property="errors",type="array"),
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=500,
     *         description="cannot generate the list",
     *     ),
     * )
     */
    public function getAllPublications(Request $request, Response $response, array $args)
    {
        return parent::getAllPublications($request, $response, $args); // TODO: Change the autogenerated stub
    }

    /**
     * @OAS\Get(
     *     path="/projects/{name}",
     *     summary="Get the project specified by its id",
     *     tags={"Project"},
     *     description="Return a single project",
     *     operationId="getProject",
     *     @OAS\Parameter(
     *          ref="#/components/parameters/Project_id"
     *     ),
     *     @OAS\Parameter(
     *          ref="#/components/parameters/API_output"
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="successful operation",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  @OAS\Property(
     *                      property="data",
     *                      type="object",
     *                      ref="#/components/schemas/Project"
     *                  ),
     *                  @OAS\Property(property="metadata",type="object"),
     *                  @OAS\Property(property="errors",type="array"),
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="the project specified by 'id' does not exist",
     *     ),
     * )
     */
    public function getProject(Request $request, Response $response, array $args)
    {
        return parent::getProject($request, $response, $args); // TODO: Change the autogenerated stub
    }

    /**
     * @OAS\Get(
     *     path="/projects/{name}/publications",
     *     summary="Get the publications associated with the project specified by its id",
     *     tags={"Project"},
     *     description="",
     *     operationId="getPublications",
     *     @OAS\Parameter(
     *          ref="#/components/parameters/Project_id"
     *     ),
     *     @OAS\Parameter(
     *          ref="#/components/parameters/API_output"
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="successful operation",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  @OAS\Property(
     *                      property="data",
     *                      type="array",
     *                      @OAS\Items(
     *                          ref="#/components/schemas/Publication"
     *                      ),
     *                  ),
     *                  @OAS\Property(property="metadata",type="object"),
     *                  @OAS\Property(property="errors",type="array"),
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="the project specified by 'id' does not exist",
     *     ),
     * )
     */
    public function getPublications(Request $request, Response $response, array $args)
    {
        return parent::getPublications($request, $response, $args); // TODO: Change the autogenerated stub
    }

    /**
     * @OAS\Get(
     *     path="/projects/{name}/slides",
     *     summary="Get the slides associated with the project specified by its id",
     *     tags={"Project"},
     *     description="",
     *     operationId="getSlides",
     *     @OAS\Parameter(
     *          ref="#/components/parameters/Project_id"
     *     ),
     *     @OAS\Parameter(
     *          ref="#/components/parameters/API_output"
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="successful operation",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  @OAS\Property(
     *                      property="data",
     *                      type="array",
     *                      @OAS\Items(
     *                          ref="#/components/schemas/Slide"
     *                      ),
     *                  ),
     *                  @OAS\Property(property="metadata",type="object"),
     *                  @OAS\Property(property="errors",type="array"),
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="the project specified by 'id' does not exist",
     *     ),
     * )
     */
    public function getSlides(Request $request, Response $response, array $args)
    {
        return parent::getSlides($request, $response, $args);
    }


    /**
     * @OAS\Get(
     *     path="/projects/{name}/images",
     *     summary="Get the images associated with the project specified by its id",
     *     tags={"Project"},
     *     description="",
     *     operationId="getImages",
     *     @OAS\Parameter(
     *          ref="#/components/parameters/Project_id"
     *     ),
     *     @OAS\Parameter(
     *          ref="#/components/parameters/API_output"
     *     ),
     *     @OAS\Response(
     *          response=200,
     *          description="successful operation",
     *          @OAS\MediaType(
     *              mediaType="application/json",
     *              @OAS\Schema(
     *                  @OAS\Property(
     *                      property="data",
     *                      type="array",
     *                      @OAS\Items(
     *                          ref="#/components/schemas/Image"
     *                      ),
     *                  ),
     *                  @OAS\Property(property="metadata",type="object"),
     *                  @OAS\Property(property="errors",type="array"),
     *              ),
     *          ),
     *     ),
     *     @OAS\Response(
     *         response=404,
     *         description="the project specified by 'id' does not exist",
     *     ),
     * )
     */
    public function getImages(Request $request, Response $response, array $args)
    {
        return parent::getImages($request, $response, $args); // TODO: Change the autogenerated stub
    }

}

