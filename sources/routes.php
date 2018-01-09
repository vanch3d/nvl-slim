<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:20
 */

use Slim\Csrf\Guard;

/**
 * Global Middlewares
 */
$app->add($container->get(Guard::class));

