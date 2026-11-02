<?php

namespace Auth\Service;

/**
 * Vytvoří přihlašovací jméno (login_name – vnitřní klíč účtu) z e-mailu.
 *
 * Pravidla (docs/vp/zadani/A1.md):
 * 1. část před @, malá písmena, diakritika na ASCII, znaky mimo [a-z0-9_] → _, více _ → jedno, _ na okrajích pryč,
 *    zkrátit na 46 znaků;
 * 2. prázdný výsledek → "ucastnik"; kratší než 5 znaků → doplnit číslem na 5 znaků (jan → jan01, ab → ab001);
 * 3. při shodě s existujícím loginem přípona 2, 3, … (petr_novak2), max. 50 znaků.
 *
 * Výsledek vyhovuje pravidlu jména \w{5,} a nikdy neobsahuje @.
 */
class LoginNameGenerator {

    public const MAX_LENGTH = 50;
    public const MIN_LENGTH = 5;
    private const BASE_MAX_LENGTH = 46;
    private const EMPTY_BASE = 'ucastnik';

    /** @var callable(string):bool */
    private $exists;

    /**
     * @param callable $exists function(string $loginName): bool – true, pokud login existuje (bez ohledu na velikost písmen)
     */
    public function __construct(callable $exists) {
        $this->exists = $exists;
    }

    public function generate(string $email): string {
        $base = self::baseFromEmail($email);
        if (strlen($base) < self::MIN_LENGTH) {
            $width = self::MIN_LENGTH - strlen($base);
            for ($i = 1; ; $i++) {
                $candidate = $base . str_pad((string) $i, $width, '0', STR_PAD_LEFT);
                if (!($this->exists)($candidate)) {
                    return $candidate;
                }
            }
        }
        if (!($this->exists)($base)) {
            return $base;
        }
        for ($i = 2; ; $i++) {
            $suffix = (string) $i;
            $candidate = substr($base, 0, self::MAX_LENGTH - strlen($suffix)) . $suffix;
            if (!($this->exists)($candidate)) {
                return $candidate;
            }
        }
    }

    /**
     * Základ jména z e-mailu (bez přípony pro jedinečnost).
     */
    public static function baseFromEmail(string $email): string {
        $local = trim($email);
        $at = strrpos($local, '@');
        if ($at !== false) {
            $local = substr($local, 0, $at);
        }
        $ascii = self::toAscii($local);
        $ascii = strtolower($ascii);
        $ascii = preg_replace('/[^a-z0-9_]/', '_', $ascii);
        $ascii = preg_replace('/_+/', '_', $ascii);
        $ascii = trim($ascii, '_');
        $ascii = rtrim(substr($ascii, 0, self::BASE_MAX_LENGTH), '_');
        return $ascii === '' ? self::EMPTY_BASE : $ascii;
    }

    private static function toAscii(string $text): string {
        if (class_exists(\Transliterator::class)) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
            if ($transliterator) {
                $result = $transliterator->transliterate($text);
                if ($result !== false) {
                    return $result;
                }
            }
        }
        $result = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return $result === false ? '' : $result;
    }
}
