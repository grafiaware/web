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
     * Zavře session pro všechny GET requesty s hlavičkou "X-Cascade" (včetně flash).
     * Pro flash cascade GET po návratu z handle session znovu otevře (reopen), aby FlashStatus mohl zapsat spotřebované messages.
     *
     * Po zavření session nelze volat Status repo get/add/remove/flush.
     * Lze volat repo->getClone() (immutable) nebo getClone(false) + replaceEntityInMemory (mutable flash).
     *
     * Pro ostatní případy se session ukládá a zavírá automaticky až na konci skriptu:
     *  - jiné než GET requesty - handler mění Status (PUT, POST)
     *  - GET bez X-Cascade = stránka z Page controleru - handler mění Status
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $isCascadeGet = $request->getMethod() === 'GET' && $request->hasHeader('X-Cascade');
        $isFlash = $isCascadeGet && $this->isFlashRequest($request);

        if ($isCascadeGet) {
            $container = $this->getApp()->getAppContainer();
            /** @var StatusDao $statusDao */
            $statusDao = $container->get(StatusDao::class);
            $statusDao->finish();
        }

        $response = $handler->handle($request);

        if ($isFlash) {
            $container = $this->getApp()->getAppContainer();
            /** @var StatusDao $statusDao */
            $statusDao = $container->get(StatusDao::class);
            $statusDao->reopen();
        }

        return $response;
    }

    private function isFlashRequest(ServerRequestInterface $request): bool {
        $path = $request->getUri()->getPath();
        return strpos($path, 'component/flash') !== false;
    }
}
