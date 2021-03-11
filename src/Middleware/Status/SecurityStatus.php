<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Middleware\Status;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Middleware\AppMiddlewareAbstract;

use Model\Repository\StatusSecurityRepo;
use StatusManager\StatusManagerInterface;

/**
 * Description of Status
 *
 * @author pes2704
 */
class SecurityStatus extends AppMiddlewareAbstract implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $container = $this->getApp()->getAppContainer();

        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
        /** @var StatusManagerInterface $statusSecurityManager */
        $statusSecurityManager = $container->get(StatusManagerInterface::class);
        if (!$statusSecurityRepo->get()) {
            $statusSecurityRepo->add($statusSecurityManager->createStatus());
        }


        return $handler->handle($request);
    }
}