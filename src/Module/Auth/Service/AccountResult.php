<?php

namespace Auth\Service;

/**
 * A6: výsledek založení účtu. Token (jen u „heslo mailem“) je čitelný jen zde – v DB je hash, posílá se jen e-mailem.
 */
final class AccountResult {

    public function __construct(
        public readonly string $loginName,
        public readonly string $email,
        public readonly string $role,
        public readonly ?string $displayName,
        public readonly ?string $setPasswordToken,
        public readonly ?string $eventsWarning,
    ) {
    }
}
