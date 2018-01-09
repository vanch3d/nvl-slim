<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 18:36
 */

namespace NVL\Middlewares;


use DI\Container;

abstract class Middleware
{
    protected $view;
    protected $session;
    protected $csrf;

    public function __construct(Container $container)
    {

    }
}