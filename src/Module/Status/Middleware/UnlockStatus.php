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

use Pes\Application\Middleware\AppMiddlewareAbstract;

use Pes\Model\Dao\StatusDao;

/**
 * Description of FinishStatus
 *
 * @author pes2704
 */
class UnlockStatus extends AppMiddlewareAbstract implements MiddlewareInterface {
    /**
     * Uvolní session lock
     * 
     * Zapíše session data do úložiště a zavře session (session_write_close). Tím uvolní session data v úložišti (např. soubor ke čtení) 
     * pro další request, který nemusí čekat nebo přestane čekat na session_start().
     * 
     * Zavře session pro: 
     * - GET requesty požadující cascade komponent (mají hlavičku "X-Cascade"), pokud to není komponent flash.
     * 
     * Zavírá session volání StatusDao::finish(). StatusDao používají všechna Status repo. Po zavření session nelze volat metody Staus repo get/add/remove.
     * Lze volat pouze repo->getClone(), ta vrací jen klon status entity ke čtení. 
     * 
     * Pro ostatní případy se session se ukládá a zavírá automaticky až na konci skriptu:
     *  - jiné než GET requesty - handler mění Status (PUT, POST)
     *  - flash komponent - je volán GET requestem, ale handler mění Status - vyzvedne a smaže flash messages
     *  - GET požaduje něco jiného než component = stránka z Page controleru - handler mění Status, ukládá menu item
     * 
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        if ($request->getMethod() == 'GET' && $request->hasHeader("X-Cascade") && !$this->isFlashRequest($request)) {
            $container = $this->getApp()->getAppContainer();
            /** @var StatusDao $statusDao */
            $statusDao = $container->get(StatusDao::class);
            // uloží data a zavře session (session_write_close)
            // finish lze data session pouze číst, nelze zapisovat
            $statusDao->finish();
        }
        return $handler->handle($request);
    }
    
    private function isFlashRequest(ServerRequestInterface $request) {
        $path = $request->getUri()->getPath();
        return strpos($path, "component/flash") !== false;        
    }
}