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
use Swagger\Annotations as SWG;

class SwaggerAPI extends APIController
{
    /**
     * @todo[vanch3d] unAPI cannot be fully described by v2; need to switch to OAS (v3)
     *
     * @SWG\Get(
     *     path="/unapi",
     *     summary="Base of the unAPI service",
     *     tags={"unAPI"},
     *     description="",
     *     operationId="unAPI",
     *     produces={ "application/xml"},
     *     @SWG\Parameter(
     *          ref="$/parameters/unapi_id"
     *     ),
     *     @SWG\Parameter(
     *          ref="$/parameters/unapi_format",
     *          required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @SWG\Response(
     *         response=300,
     *         description="format is missing; return a list of supported formats for the given id",
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="id is missing or not defined",
     *     ),
     *     @SWG\Response(
     *         response=406,
     *         description="this format is not recognised",
     *     ),
     * )
     */
    public function unAPI(Request $request, Response $response)
    {
        return parent::unAPI($request, $response); // TODO: Change the autogenerated stub
    }

    /**
     * @SWG\Get(
     *     path="/projects",
     *     summary="Get the list of all projects in the application",
     *     tags={"Project"},
     *     description="",
     *     operationId="getAllProjects",
     *     produces={
     *          "application/json",
     *          "application/xml"
     *     },

     *     @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          ref="$/responses/JSON",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      ref="#/definitions/Project"
     *                  )
     *              ),
     *          ),
     *     ),
     *     @SWG\Response(
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
     * @SWG\Get(
     *     path="/publications",
     *     summary="Get the list of all publications",
     *     tags={"Publication"},
     *     description="",
     *     operationId="getAllPublications",
     *     @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          ref="$/responses/JSON",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      ref="#/definitions/Publication"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="meta",
     *                  @SWG\Property(
     *                      property="last-modified-version",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="count",
     *                      type="integer",
     *                      minimum=0
     *                  )
     *              ),
     *          ),
     *     ),
     *     @SWG\Response(
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
     * @SWG\Get(
     *     path="/projects/{name}",
     *     summary="Get the project specified by its id",
     *     tags={"Project"},
     *     description="",
     *     operationId="getProject",
     *     @SWG\Parameter(
     *          ref="$/parameters/project_id"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          ref="$/responses/JSON",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Project"
     *              ),
     *          ),
     *     ),
     *     @SWG\Response(
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
     * @SWG\Get(
     *     path="/projects/{name}/publications",
     *     summary="Get the publications associated with the project specified by its id",
     *     tags={"Project"},
     *     description="",
     *     operationId="getPublications",
     *     @SWG\Parameter(
     *          ref="$/parameters/project_id"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *          ref="$/responses/JSON",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      ref="#/definitions/Publication"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="meta",
     *                  @SWG\Property(
     *                      property="last-modified-version",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="count",
     *                      type="integer",
     *                      minimum=0
     *                  )
     *              ),
     *          ),
     *     ),
     *     @SWG\Response(
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
     * @SWG\Get(
     *     path="/projects/{name}/slides",
     *     summary="Get the slides associated with the project specified by its id",
     *     tags={"Project"},
     *     description="",
     *     operationId="getSlides",
     *     @SWG\Parameter(
     *          ref="$/parameters/project_id"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *          ref="$/responses/JSON",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      ref="#/definitions/Slide"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="meta",
     *                  @SWG\Property(
     *                      property="Name",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="Count",
     *                      type="integer",
     *                      minimum=0
     *                  )
     *              ),
     *          ),
     *     ),
     *     @SWG\Response(
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
     * @SWG\Get(
     *     path="/projects/{name}/images",
     *     summary="Get the images associated with the project specified by its id",
     *     tags={"Project"},
     *     description="",
     *     operationId="getImages",
     *     @SWG\Parameter(
     *          ref="$/parameters/project_id"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @SWG\Response(
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

