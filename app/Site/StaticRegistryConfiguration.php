<?php

namespace Site;

/**
 * Sdílená konfigurace static registry pro všechny site.
 *
 * pushConfig  — red modul (odesílá metadata)
 * receiveConfig — auth/events (přijímá do SQLite, validuje pathPrefix)
 *
 * Token musí být shodný na red i cílovém serveru. V produkci změňte
 * 'dev-static-registry-{site}' a nastavte moduleBaseUrls na remote URL.
 */
class StaticRegistryConfiguration {

    /**
     * Konfigurace red modulu (push metadata na auth/events).
     *
     * moduleBaseUrls prázdné = same-host fallback (vývoj). V multi-server
     * nasazení: 'events' => 'https://events.example.com/app/', ...
     *
     * @param string $siteCode např. najdisi
     */
    public static function pushConfig(string $siteCode): array {
        $token = 'dev-static-registry-' . $siteCode;
        return [
            'staticRegistry.siteCode' => $siteCode,
            'staticRegistry.token' => $token,
            'staticRegistry.push.enabled' => true,
            'staticRegistry.push.moduleBaseUrls' => [
                'events' => '',
                'auth' => '',
            ],
            'staticRegistry.templatePrefixes' => [
                'events' => 'events/',
                'auth' => 'auth/',
            ],
        ];
    }

    /**
     * Konfigurace auth/events modulu (příjem metadat).
     *
     * @param string $siteCode např. najdisi
     * @param string $pathPrefix např. events/ nebo auth/
     * @param string $sqlitePath cesta k sqlite souboru
     */
    public static function receiveConfig(string $siteCode, string $pathPrefix, string $sqlitePath): array {
        $token = 'dev-static-registry-' . $siteCode;
        return [
            'staticRegistry.siteCode' => $siteCode,
            'staticRegistry.token' => $token,
            'staticRegistry.pathPrefix' => $pathPrefix,
            'staticRegistry.storage.sqlitePath' => $sqlitePath,
        ];
    }
}
