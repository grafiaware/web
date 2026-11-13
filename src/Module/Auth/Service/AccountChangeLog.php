<?php

namespace Auth\Service;

use Psr\Log\LoggerInterface;

/**
 * A9: log změn údajů účtů v administraci (AccountChanges.log) – kdo, komu, který údaj. Bez hodnot (e-mailů, jmen).
 */
class AccountChangeLog {

    public function __construct(private LoggerInterface $logger) {
    }

    /**
     * @param string $what např. 'e-mail', 'osobní jméno', 'založení účtu'
     */
    public function record(string $actorLogin, string $targetLogin, string $what): void {
        $this->logger->info(sprintf('Změna účtu: %s → účet %s: %s.', $actorLogin !== '' ? $actorLogin : '?', $targetLogin, $what));
    }
}
