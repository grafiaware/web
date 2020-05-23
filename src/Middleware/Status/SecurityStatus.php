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
use StatusManager\StatusSecurityManagerInterface;

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
        /** @var StatusSecurityManagerInterface $statusSecurityManager */
        $statusSecurityManager = $container->get(StatusSecurityManagerInterface::class);
        if (!$statusSecurityRepo->get()) {
            $statusSecurityRepo->add($statusSecurityManager->createSecurityStatus());
        }


        return $handler->handle($request);
    }
}