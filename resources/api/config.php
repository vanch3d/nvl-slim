<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 15:55
 */

const APP_DEBUG = true;

/**
 * @deprecated not in use at the moment, both servers are in the specification
 */
define("SWAGGER_HOST", APP_DEBUG === true ?
    "http://local.nvl.calques3d.org" :
    "http://nvl.calques3d.org");