<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:23
 */

use Swagger\Annotations as OAS;

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
 * @OAS\OpenApi(
 *     openapi= SWAGGER_VERSION,
 *     @OAS\Info(
 *          version= API_VERSION,
 *          description="A Swagger/OAS (v3) description of the nvl-slim API.",
 *          title=  API_NAME,
 *          @OAS\License(
 *              name="MIT",
 *              url="https://github.com/vanch3d/nvl-slim/blob/master/LICENSE"
 *          ),
 *          @OAS\Contact(
 *              name="vanch3d",
 *              url="https://github.com/vanch3d"
 *          ),
 *     )
 * )
 *
 * @OAS\Server(
 *     description="nvl-slim DEV server",
 *     url="http://local.nvl.calques3d.org/api"
 * )
 * @OAS\Server(
 *     description="nvl-slim PRODUCTION server",
 *     url="http://nvl.calques3d.org/api"
 * )
 *
 */

// ------------------------------------------------------------------------------------------
// Definitions
// ------------------------------------------------------------------------------------------

/**
 * @OAS\Parameter(
 *      parameter="Project_id",
 *      name="name",
 *      in="path",
 *      description="the unique id of a research project",
 *      required=true,
 *      @OAS\Schema(
 *          type="string"
 *      ),
 * )
 *
 *
 *  * @OAS\Parameter(
 *      parameter="API_output",
 *      name="output",
 *      in="query",
 *      description="Force the format of the response of the API request",
 *      required=false,
 *      @OAS\Schema(
 *          type="string",
 *          enum={"json","xml"}
 *      ),
 * )

 */

// ------------------------------------------------------------------------------------------
// Tags
// ------------------------------------------------------------------------------------------

/**
 * @OAS\Tag(
 *     name="unAPI",
 *     description="A simple micro-service for resolving publication references",
 *     @OAS\ExternalDocumentation(
 *         description="Read more",
 *         url="https://goo.gl/gQTsEf"
 *     )
 * )
 *
 * @OAS\Tag(
 *     name="Project",
 *     description="Everything about the research projects"
 * )
 *
 * @OAS\Tag(
 *     name="Publication",
 *     description="Everything about the research publications"
 * )
 */