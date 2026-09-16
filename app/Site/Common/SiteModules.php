<?php

namespace Site\Common;

use LogicException;

/**
 * Detekce dostupných modulů site podle existence konfiguračních tříd.
 */
final class SiteModules {

    /** @return list<string> */
    public static function knownSites(): array {
        return ['NajdiSi', 'Grafia', 'OtevreneAteliery', 'VeletrhPrace', 'TydenZdravi'];
    }

    public static function siteNamespace(string $site): string {
        return 'Site\\' . $site;
    }

    public static function sitePath(string $site): string {
        return 'app/Site/' . $site . '/';
    }

    /**
     * @return list<string> ConfigSchema::MODULE_*
     */
    public static function enabledModules(string $site): array {
        $ns = self::siteNamespace($site);
        $enabled = [ConfigSchema::MODULE_WEB];
        if (class_exists($ns . '\\ConfigurationRed')) {
            $enabled[] = ConfigSchema::MODULE_RED;
        }
        if (class_exists($ns . '\\ConfigurationAuth')) {
            $enabled[] = ConfigSchema::MODULE_AUTH;
        }
        if (class_exists($ns . '\\ConfigurationEvents')) {
            $enabled[] = ConfigSchema::MODULE_EVENTS;
        }
        if (class_exists($ns . '\\ConfigurationBuild')) {
            $enabled[] = ConfigSchema::MODULE_BUILD;
        }
        return $enabled;
    }

    public static function hasModule(string $site, string $module): bool {
        return in_array($module, self::enabledModules($site), true);
    }

    public static function assertKnownSite(string $site): void {
        if (!in_array($site, self::knownSites(), true)) {
            throw new LogicException("Unknown site '$site'. Allowed: " . implode(', ', self::knownSites()));
        }
    }
}
