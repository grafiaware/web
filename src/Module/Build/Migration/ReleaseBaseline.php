<?php

namespace Build\Migration;

/**
 * Produktová baseline verze (rok), konzistentní s konvencí typu company_version = '2025'.
 * Není to 2025.x ani timestamp souboru migrace.
 */
final class ReleaseBaseline
{
    /** Aktuální produkční baseline — výchozí pro Build panel. */
    public const PRODUCTION = '2025';

    public static function isValid(string $release): bool
    {
        return (bool) preg_match('/^\d{4}$/', $release);
    }

    /**
     * Rok z verzovaného souboru migrace (YYYYMMDDHHMMSS_slug → YYYY).
     */
    public static function yearFromFileVersion(string $fileVersion): ?string
    {
        if (preg_match('/^(\d{4})/', $fileVersion, $m) !== 1) {
            return null;
        }

        return $m[1];
    }

    /**
     * Souborová migrace je pokrytá release markerem, pokud existuje marker YYYY
     * se stejným nebo vyšším rokem než rok v názvu souboru.
     *
     * @param array<string, true> $appliedSet
     */
    public static function isCoveredByReleaseMarkers(string $fileVersion, array $appliedSet): bool
    {
        $year = self::yearFromFileVersion($fileVersion);
        if ($year === null) {
            return false;
        }
        $fileYear = (int) $year;
        foreach (array_keys($appliedSet) as $marker) {
            if (self::isValid((string) $marker) && (int) $marker >= $fileYear) {
                return true;
            }
        }

        return false;
    }
}
