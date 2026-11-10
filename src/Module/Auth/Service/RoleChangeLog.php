<?php

namespace Auth\Service;

use Psr\Log\LoggerInterface;

/**
 * A9: log změn rolí (RoleChanges.log) – kdo, komu, z jaké role na jakou. Bez e-mailů a hesel.
 */
class RoleChangeLog {

    public function __construct(private LoggerInterface $logger) {
    }

    public function record(string $actorLogin, string $targetLogin, ?string $oldRole, ?string $newRole): void {
        $this->logger->info(sprintf('Změna role: %s → účet %s: %s → %s.',
            $actorLogin !== '' ? $actorLogin : '?', $targetLogin, $oldRole ?? 'bez role', $newRole ?? 'bez role'));
    }
}
