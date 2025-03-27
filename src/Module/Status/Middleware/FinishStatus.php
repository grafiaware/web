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
 * Description of FinishStatus
 *
 * @author pes2704
 */
class FinishStatus extends AppMiddlewareAbstract implements MiddlewareInterface {
    /**
     * Uvolní session lock
     * 
     * Zapíše session data do úložiště a zavře session pro requesty, které v handleru nemění Status. Tím uvolní session data v úložišti (např. soubor ke čtení) 
     * pro další request, který nemusí čekat nebo přestane čekat na session_start().
     * 
     * Requesty, které v handleru nemění Status jsou GET requesty požadující cascade komponent, pokud to není komponent flash.
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
            $statusDao->finish();  // uloží data a zavře session (session_write_close)
        }
        return $handler->handle($request);
    }
    
    private function isFlashRequest(ServerRequestInterface $request) {
        $path = $request->getUri()->getPath();
        return strpos($path, "component/flash") !== false;        
    }
}