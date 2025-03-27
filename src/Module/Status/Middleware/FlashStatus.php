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

use Status\Model\Entity\Flash;
use Status\Model\Repository\StatusFlashRepo;

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

        $statusFlash = $statusFlashRepo->get();
        if (!isset($statusFlash)) {
            $statusFlash = new Flash();
            $statusFlashRepo->add($statusFlash);
        }
        
        // je to GET komponent -> flush
        //TODO: flash nenačítat jako komponent, vložit vždy do layoutu
        if ($request->getMethod() == 'GET') {  // && $request->hasHeader("X-Cascade")) {  
            $statusFlash->retrieveMessages();   // připraví data pro metodu getMessages() a smaže v entotě z uložených dt
            $statusFlashRepo->flush();   // uloží data a pokud je poslední status middleware ve stacku zavře session (session_write_close)
        }
        
        ###
        $response = $handler->handle($request);
        ###
        
        if ($request->getMethod() != 'GET') {
            $statusFlash->storeMessages();
        }
//        $statusFlashRepo->flush();   // uloží data a pokud je poslední status middleware ve stacku zavře session (session_write_close)
        return $response;
    }
}