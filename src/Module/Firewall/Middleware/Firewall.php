<?php
namespace Firewall\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Http\Headers;
use Pes\Http\Factory\BodyFactory;
use Pes\Http\Response;

use Firewall\Middleware\Rule\RoleInterface;

class Firewall implements MiddlewareInterface {

    private $accessor;

    public function __construct(RoleInterface $accessor) {
        $this->accessor = $accessor;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if ($this->accessor->granted()) {
            $response = $handler->handle($request);
        } else {
            $headers = new Headers();
            $body = (new BodyFactory())->createStream($this->accessor->restrictMessage());
            $body->rewind();
            $response = new Response(403, $headers, $body);
        }
        return $response;
    }
}


