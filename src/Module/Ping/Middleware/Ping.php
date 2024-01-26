<?php
namespace Ping\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Http\Factory\ResponseFactory;
use Pes\Logger\FileLogger;


class Ping implements MiddlewareInterface {

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
//        $response = $handler->handle($request);
        $response = (new ResponseFactory())->createResponse();
        ####  body  ####
        $size = $response->getBody()->write("Ping");
        $response->getBody()->rewind();
        return $response;
    }
}


