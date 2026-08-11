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
use Pes\Session\SessionStatusHandlerInterface;

use Status\Session\SessionUnlockPolicy;

/**
 * Uvolní session lock
 *
 * Politika finish/reopen: {@see SessionUnlockPolicy}.
 *
 * Po zavření session nelze volat Status repo get/add/remove/flush.
 * Lze volat repo->getClone() (immutable) nebo getClone(false) + replaceEntityInMemory (mutable snapshot).
 *
 * Pro ostatní případy se session ukládá a zavírá automaticky až na konci skriptu:
 *  - jiné než GET requesty - handler mění Status (PUT, POST)
 *  - GET bez cascade hlavičky = stránka z Page controleru - handler mění Status
 *
 * @author pes2704
 */
class UnlockStatus extends AppMiddlewareAbstract implements MiddlewareInterface {

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $container = $this->getApp()->getAppContainer();
        /** @var SessionUnlockPolicy $policy */
        $policy = $container->get(SessionUnlockPolicy::class);
        /** @var SessionStatusHandlerInterface $sessionHandler */
        $sessionHandler = $container->get(SessionStatusHandlerInterface::class);
        $sessionIsNew = $sessionHandler->isNew();

        if ($policy->isNewSessionCascadeAnomaly($request, $sessionIsNew)) {
            user_error(
                sprintf(
                    'SessionUnlockPolicy anomaly: new session with %s on %s %s — session finish skipped.',
                    SessionUnlockPolicy::CASCADE_HEADER,
                    $request->getMethod(),
                    $request->getUri()->getPath()
                ),
                E_USER_WARNING
            );
        }

        $shouldFinish = $policy->shouldFinish($request, $sessionIsNew);
        $needsReopen = $shouldFinish && $policy->needsReopen($request);

        if ($shouldFinish) {
            /** @var StatusDao $statusDao */
            $statusDao = $container->get(StatusDao::class);
            $statusDao->finish();
        }

        $response = $handler->handle($request);

        if ($needsReopen) {
            /** @var StatusDao $statusDao */
            $statusDao = $container->get(StatusDao::class);
            $statusDao->reopen();
        }

        return $response;
    }
}
