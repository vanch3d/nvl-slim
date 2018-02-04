<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 04/02/2018
 * Time: 15:55
 */

const APP_DEBUG = true;

define("SWAGGER_HOST", APP_DEBUG === true ?
    "local.nvl.calques3d.org" :
    "nvl.calques3d.org");