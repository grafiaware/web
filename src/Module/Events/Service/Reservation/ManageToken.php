<?php

namespace Events\Service\Reservation;

/**
 * R1 (#48): token odkazu pro správu rezervace – 32 náhodných bajtů v base64url (jako přihlašovací odkaz A1).
 * Token je jen v odkazu v e-mailu pedagogovi; v DB je jen SHA-256 hash (hex, 64 znaků).
 */
final class ManageToken {

    public static function generate(): string {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public static function hash(string $token): string {
        return hash('sha256', $token);
    }

    /** 32 bajtů base64url = 43 znaků z abecedy A–Z a–z 0–9 - _. */
    public static function isWellFormed(string $token): bool {
        return (bool) preg_match('/^[A-Za-z0-9_-]{43}$/', $token);
    }
}
