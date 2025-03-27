<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Status\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Middleware\AppMiddlewareAbstract;

use Model\Dao\StatusDao;

/**
 * Description of Status
 *
 * @author pes2704
 */
class FinishStatus extends AppMiddlewareAbstract implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        if ($request->getMethod() == 'GET') { // && $request->hasHeader("X-Cascade")) {
            $container = $this->getApp()->getAppContainer();
            /** @var StatusDao $statusDao */
            $statusDao = $container->get(StatusDao::class);
            $statusDao->finish();  // uloží data a zavře session (session_write_close)
        }
        return $handler->handle($request);
    }
}