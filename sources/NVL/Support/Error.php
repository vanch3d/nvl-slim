<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 09/01/2018
 * Time: 20:23
 */

namespace NVL\Support;


use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Error extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct(bool $displayErrorDetails, Logger $logger)
    {
        parent::__construct($displayErrorDetails);
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        // Log the message
        $this->logger->error($exception->getMessage());
        return parent::__invoke($request, $response, $exception);
    }

}