<?php
namespace RequestLogger\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Logger\FileLogger;
use Pes\Http\Helper\RequestDumper;

class RequestLogger implements MiddlewareInterface {

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if ($GLOBALS['development']) {
            $requestsLogger = FileLogger::getInstance('Logs', 'RequestLogger.log', FileLogger::REWRITE_LOG);
            foreach(RequestDumper::dump($request) as $item) {
                $requestsLogger->info($item);
            }
        }
        $response = $handler->handle($request);
        return $response;
    }
}


