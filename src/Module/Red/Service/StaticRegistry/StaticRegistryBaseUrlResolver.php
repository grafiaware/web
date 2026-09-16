<?php

namespace Red\Service\StaticRegistry;

use Site\ConfigurationCache;

/**
 * Sdílené skládání base URL pro server-to-server volání static registry.
 *
 * Priorita: explicitní $baseUrl → moduleBaseUrls → same-host včetně SCRIPT_NAME dir
 * (podadresář aplikace, stejně jako UriInfo::getSubdomainPath).
 */
final class StaticRegistryBaseUrlResolver {

    public static function resolve(string $apiModule, ?string $baseUrl = null): string {
        $configuredBase = ConfigurationCache::staticRegistry()['staticRegistry.push.moduleBaseUrls'][$apiModule] ?? null;
        if ($baseUrl !== null && $baseUrl !== '') {
            return rtrim($baseUrl, '/') . '/';
        }
        if (is_string($configuredBase) && $configuredBase !== '') {
            return rtrim($configuredBase, '/') . '/';
        }
        return self::sameHostBaseUrl();
    }

    /**
     * Same-host fallback: scheme + HTTP_HOST + adresář SCRIPT_NAME (např. /www/).
     */
    public static function sameHostBaseUrl(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        if ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '') {
            return "$scheme://$host/";
        }
        return "$scheme://$host" . rtrim($scriptDir, '/') . '/';
    }
}
