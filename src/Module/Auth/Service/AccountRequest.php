<?php

namespace Auth\Service;

/**
 * A6: vstup pro založení účtu administrátorem (docs/vp/zadani/A6.md).
 */
final class AccountRequest {

    public function __construct(
        public readonly string $email,
        public readonly string $role,
        public readonly string $loginName = '',
        public readonly string $password = '',
        public readonly bool $passwordByMail = false,
        public readonly ?string $displayName = null,
        public readonly int $setPasswordTtlHours = 72,
    ) {
    }
}
