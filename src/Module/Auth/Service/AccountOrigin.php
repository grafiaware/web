<?php

namespace Auth\Service;

/**
 * A9: typ účtu podle vzniku (login_profile.origin).
 *
 * self   – registrace přes veřejný formulář; identita = e-mail, e-mail se nemění.
 * admin  – založen v administraci; identita = přihlašovací jméno, e-mail je vedlejší údaj.
 * legacy – starší účet bez registrace a e-mailu.
 * Když origin v DB chybí (účet vzniklý po migraci registrací), odvodí se: registrace s e-mailem → self, jinak legacy.
 */
final class AccountOrigin {

    public const SELF = 'self';
    public const ADMIN = 'admin';
    public const LEGACY = 'legacy';

    public static function resolve(?string $stored, ?string $registrationEmail): string {
        if (in_array($stored, [self::SELF, self::ADMIN, self::LEGACY], true)) {
            return $stored;
        }
        return trim((string) $registrationEmail) !== '' ? self::SELF : self::LEGACY;
    }

    public static function label(string $origin): string {
        return match ($origin) {
            self::SELF => 'registrace',
            self::ADMIN => 'administrace',
            default => 'starší účet',
        };
    }
}
