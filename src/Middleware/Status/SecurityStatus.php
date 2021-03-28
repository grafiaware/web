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

use Model\Entity\StatusSecurity;
use Model\Repository\StatusSecurityRepo;

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
        /** @var StatusSecurity $statusSecurity */
        $statusSecurity = $statusSecurityRepo->get();

        // po vypršení session - security status není persisted, ale také nemá objekt UserActions (a vznikají chyby pří dotazech na userActions->xxx())
        //object(Model\Entity\StatusSecurity)[178]
        //  private 'loginAggregate' => null
        //  private 'userActions' => null
        //  private 'persisted' (Model\Entity\EntityAbstract) => boolean false
        //  private 'locked' (Model\Entity\EntityAbstract) => boolean false

        // obnoví security status s tím, že login aggregate je null - pro případny privátní obsah musí být vnořen Login middleware
        if (!isset($statusSecurity) OR !$statusSecurity) {
            $statusSecurityRepo->add((new StatusSecurity())->renewSecurityStatus());    // obnoví status stím, že login aggregate je null
        } elseif ( !$statusSecurity->hasSecurityContext()) {
            $statusSecurity->renewSecurityStatus();
        }

        return $handler->handle($request);
    }
}