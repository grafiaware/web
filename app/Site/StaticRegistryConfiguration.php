<?php

namespace Site;

/**
 * Sdílená konfigurace static registry pro všechny site.
 */
class StaticRegistryConfiguration {

    /**
     * Konfigurace red modulu (push metadata na auth/events).
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
