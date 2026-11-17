<?php

namespace Events\Service\Lottery;

/**
 * D1: osobní kód návštěvníka pro QR – 12 znaků z abecedy bez zaměnitelných znaků (0/O, 1/I).
 * Kód je identifikátor (nepřihlašuje, jen vydá nebo prodlouží los), ne tajný údaj.
 */
final class VisitorCodeGenerator {

    public const ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    public const LENGTH = 12;

    public function __construct(private RandomSourceInterface $random) {
    }

    public function generate(): string {
        $max = strlen(self::ALPHABET) - 1;
        $code = '';
        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[$this->random->int(0, $max)];
        }
        return $code;
    }

    public static function isWellFormed(string $code): bool {
        return (bool) preg_match('/^[' . self::ALPHABET . ']{' . self::LENGTH . '}$/', $code);
    }

    /**
     * Z naskenovaného textu (adresa .../events/v1/scan/<kód> nebo samotný kód) vrátí kód, nebo null.
     */
    public static function fromScannedText(string $text): ?string {
        $text = trim($text);
        if (preg_match('~/scan/([A-Za-z0-9]+)/?(?:[?#].*)?$~', $text, $m)) {
            $text = $m[1];
        }
        $code = strtoupper($text);
        return self::isWellFormed($code) ? $code : null;
    }
}
