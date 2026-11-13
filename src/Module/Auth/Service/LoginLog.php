<?php

namespace Auth\Service;

use Psr\Log\LoggerInterface;

/**
 * A9: logy přihlášení – LoginSelf.log (účty z registrace) a LoginAdmin.log (účty z administrace, starší účty,
 * global_role_grantor a neznámé účty). Nikdy hesla, tokeny ani e-maily; u neznámého účtu ani zadané jméno
 * (uživatelé občas napíšou heslo do pole jméno).
 */
class LoginLog {

    public function __construct(
        private LoggerInterface $selfLogger,
        private LoggerInterface $adminLogger,
    ) {
    }

    /**
     * @param string|null $origin AccountOrigin::SELF | ADMIN | LEGACY | null (neznámý účet, global_role_grantor)
     * @param string $method 'heslo', 'odkaz', 'potvrzení registrace', 'nastavení hesla'
     */
    public function success(string $loginName, ?string $origin, string $method): void {
        $this->logger($origin)->info(sprintf('Přihlášení OK: %s (%s, %s).', $loginName, $origin ?? 'konfigurace', $method));
    }

    public function failure(?string $loginName, ?string $origin, string $reason): void {
        $who = $loginName === null ? 'neznámý účet' : $loginName;
        $this->logger($origin)->warning(sprintf('Přihlášení NEÚSPĚŠNÉ: %s (%s).', $who, $reason));
    }

    private function logger(?string $origin): LoggerInterface {
        return $origin === AccountOrigin::SELF ? $this->selfLogger : $this->adminLogger;
    }
}
