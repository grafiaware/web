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

use Status\Model\Entity\Security;
use Status\Model\Repository\StatusSecurityRepo;
use Red\Model\Entity\EditorActions;

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
        /** @var Security $statusSecurity */
        $statusSecurity = $statusSecurityRepo->get();

        // obnoví security status s tím, že login aggregate je null - pro případny privátní obsah musí být vnořen Login middleware
        if (!isset($statusSecurity)) {
            $statusSecurity = new Security();
            $statusSecurityRepo->add($statusSecurity); 
        }
        if ( !$statusSecurity->hasValidSecurityContext()) {
            $statusSecurity->removeContext();
        }
        
        if ($request->getMethod() == 'GET') {
            $statusSecurityRepo->flush();    // uloží data a pokud je poslední status middleware ve stacku zavře session (session_write_close)
        }
        
        ###
        $response = $handler->handle($request);
        ###
        
//        $statusSecurityRepo->flush();
        return $response;
    }
}