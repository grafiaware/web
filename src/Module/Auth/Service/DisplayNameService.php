<?php

namespace Auth\Service;

use Auth\Model\Dao\LoginProfileDao;

/**
 * Osobní jméno uživatele (A8, docs/vp/zadani/A8.md) – normalizace, čtení a uložení.
 * Osobní jméno není klíč ani přihlašovací údaj; přihlašovací jméno (login_name) zůstává beze změny.
 */
class DisplayNameService {

    public const MAX_LENGTH = 100;

    public function __construct(private LoginProfileDao $loginProfileDao) {
    }

    /**
     * Ořízne mezery, sloučí bílé znaky do jedné mezery, odstraní řídicí znaky, zkrátí na MAX_LENGTH znaků.
     * Prázdný výsledek = null (smazat).
     */
    public static function normalize(?string $displayName): ?string {
        if ($displayName === null) {
            return null;
        }
        if (!mb_check_encoding($displayName, 'UTF-8')) {
            $displayName = mb_convert_encoding($displayName, 'UTF-8', 'UTF-8');
        }
        $name = preg_replace('/[\p{Cc}\p{Cf}]+/u', ' ', $displayName) ?? '';
        $name = trim(preg_replace('/\s+/u', ' ', $name) ?? '');
        if ($name === '') {
            return null;
        }
        return mb_substr($name, 0, self::MAX_LENGTH);
    }

    public function get(string $loginName): ?string {
        return $this->loginProfileDao->getDisplayName($loginName);
    }

    /**
     * Uloží normalizované jméno a vrátí ho (null = smazáno).
     */
    public function set(string $loginName, ?string $displayName): ?string {
        $normalized = self::normalize($displayName);
        $this->loginProfileDao->setDisplayName($loginName, $normalized);
        return $normalized;
    }
}
