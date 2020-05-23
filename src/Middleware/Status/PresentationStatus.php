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

use Container\HierarchyContainerConfigurator;
use Model\Repository\StatusPresentationRepo;
use StatusManager\StatusPresentationManagerInterface;

/**
 * Description of Status
 *
 * @author pes2704
 */
class PresentationStatus extends AppMiddlewareAbstract implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // potřebuje noovu databázi -> hierarchy konfigurator
        $container =
                (new HierarchyContainerConfigurator())->configure($this->getApp()->getAppContainer());

        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentationRepo = $container->get(StatusPresentationRepo::class);
        /** @var StatusPresentationManagerInterface $statusPresentationManager */
        $statusPresentationManager = $container->get(StatusPresentationManagerInterface::class);
        $statusPresentation = $statusPresentationRepo->get();
        if (!isset($statusPresentation)) {
            $statusPresentation = $statusPresentationManager->createPresentationStatus($request);
            $statusPresentationRepo->add($statusPresentation);
        }

        $statusPresentationManager->regenerateStatusPresentation($statusPresentation, $request);

        return $handler->handle($request);
    }
}