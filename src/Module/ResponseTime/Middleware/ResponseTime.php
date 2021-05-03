<?php
namespace Module\ResponseTime\Middleware;

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
    const HEADER = 'X-Response-Time';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $server = $request->getServerParams();
        $startTime = $server['REQUEST_TIME_FLOAT'] ?? microtime(true);
        $response = $handler->handle($request);
        return $response->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));
    }
}
