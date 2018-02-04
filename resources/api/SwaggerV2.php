<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:23
 */

use Swagger\Annotations as SWG;

/**
 * The Swagger description of the API is managed by Doctrine-type annotations.
 * See https://github.com/zircote/swagger-php
 * For the time being, annotations are being created separately from the main code,
 * given the in-progress nature of the process and the verbosity of the docblocks.
 * The API controller has been extended with its own swagger 'wrapper' so that
 * all the API routes are documented accordingly
 * When mature, the annotations will be copied and maintained in the main code.
 *
 * @todo[vanch3d] Move all Swaggers annotations to class/method when stable
 *
 */

// ------------------------------------------------------------------------------------------
// Metadata
// ------------------------------------------------------------------------------------------

/**
 * @SWG\Swagger(
 *     swagger="2.0",
 *     @SWG\Info(
 *          version="0.1",
 *          description="A Swagger (v2) description of the nvl-slim API.",
 *          title="Swagger nvl-slim",
 *          @SWG\License(
 *              name="MIT",
 *              url="https://github.com/vanch3d/nvl-slim/blob/master/LICENSE"
 *          ),
 *          @SWG\Contact(
 *              name="vanch3d",
 *              url="https://github.com/vanch3d",
 *              email="nicolas.github@calques3d.org"
 *          ),
 *     ),
 *     host=SWAGGER_HOST,
 *     basePath="/api",
 *     schemes={
 *          "http"
 *     },
 *     consumes={
 *          "application/json"
 *     },
 *     produces={
 *          "application/json"
 *     }
 * )
 */

// ------------------------------------------------------------------------------------------
// Definitions
// ------------------------------------------------------------------------------------------

/**
 * @SWG\Parameter(
 *     parameter="unapi_id",
 *     name="id",
 *     in="query",
 *     type="string",
 *     description="the unique id of the publication to be retrieved",
 * )
 *
 * @SWG\Parameter(
 *     parameter="unapi_format",
 *     name="format",
 *     in="query",
 *     type="string",
 *     description="the format of the publication record to be retrieved"
 * )
 *
 * @SWG\Parameter(
 *     parameter="project_id",
 *     name="name",
 *     type="string",
 *     required=true,
 *     in="path",
 *     description="the unique id of a research project",
 * )
 */

// ------------------------------------------------------------------------------------------
// Tags
// ------------------------------------------------------------------------------------------

/**
 * @SWG\Tag(
 *     name="unAPI",
 *     description="A simple micro-service for resolving publication references"
 * )
 *
 * @SWG\Tag(
 *     name="Project",
 *     description="Everything about the research projects"
 * )
 *
 * @SWG\Tag(
 *     name="Publication",
 *     description="Everything about the research publications"
 * )
 */