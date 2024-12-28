<?php
namespace ResponseTime\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResponseTime
 *
 * @author pes2704
 */
class ResponseTime implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $server = $request->getServerParams();
        $requestTime = $server['REQUEST_TIME_FLOAT'];
        $startTime = microtime(true);
        $response = $handler->handle($request);
        if (isset($requestTime)) {
            $response = $response->withHeader('X-Response-Time', sprintf('%2.3fms', (microtime(true) - $requestTime) * 1000));            
        }
        return $response->withHeader('X-Working-Time', sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));
    }
}
