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

use Model\Repository\StatusFlashRepo;
use StatusManager\StatusFlashManagerInterface;

/**
 * Description of Status
 *
 * @author pes2704
 */
class FlashStatus extends AppMiddlewareAbstract implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $container = $this->getApp()->getAppContainer();

        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $container->get(StatusFlashRepo::class);
        /** @var StatusFlashManagerInterface $statusFlashManager */
        $statusFlashManager = $container->get(StatusFlashManagerInterface::class);

        $statusFlash = $statusFlashRepo->get();
        if (!isset($statusFlash)) {
            $statusFlash = $statusFlashManager->createStatusFlash();
            $statusFlashRepo->add($statusFlash);
        }

        $statusFlashManager->renewStatusFlash($statusFlash, $request);

        return $handler->handle($request);
    }
}