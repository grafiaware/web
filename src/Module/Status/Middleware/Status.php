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

use Status\Model\Entity\StatusSecurity;
use Status\Model\Repository\StatusSecurityRepo;
use Red\Model\Entity\EditorActions;

/**
 * Description of Status
 *
 * @author pes2704
 */
class Status extends AppMiddlewareAbstract implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $this->securityStatusBeforeHandle();

        return $handler->handle($request);
    }
    
    private function securityStatusBeforeHandle() {
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
        if (!isset($statusSecurity)) {
            $statusSecurity = new StatusSecurity();
            $statusSecurityRepo->add($statusSecurity); 
        }
        if ( !$statusSecurity->hasValidSecurityContext()) {
            $statusSecurity->removeContext();
        }        
    }
    
    
    private function flashStatusBeforeHandle($param) {
        $container = $this->getApp()->getAppContainer();

        /** @var StatusFlashRepo $statusFlashRepo */
        $statusFlashRepo = $container->get(StatusFlashRepo::class);

        $statusFlash = $statusFlashRepo->get();
        if (!isset($statusFlash)) {
            $statusFlash = new StatusFlash();
            $statusFlashRepo->add($statusFlash);
        }
        $statusFlash->beforeHandle($request);
        if ($request->getMethod() == 'GET') {
            unset($statusFlashRepo);   // uloží data a zavře session (session_write_close)
            $sessionClosed = true;
        } else {
            $sessionClosed = false;            
        }
        
    }
    
}