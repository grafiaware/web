<?php
namespace Middleware\Logged;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Http\Headers;
use Pes\Http\Factory\BodyFactory;
use Pes\Http\Response;

use Middleware\Logged\Service\AccessorInterface;

class LoggedAccess implements MiddlewareInterface {

    private $accessor;

    public function __construct(AccessorInterface $accessor) {
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
            $body = (new BodyFactory())->createStream("Přístup mají pouze přihlášení uživatelé.");
            $body->rewind();
            $response = new Response(403, $headers, $body);
        }
        return $response;
    }
}


