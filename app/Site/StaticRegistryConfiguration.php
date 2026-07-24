<?php

namespace Site;

/**
 * Sdílená konfigurace static registry pro všechny site.
 *
 * pushConfig  — red modul (odesílá metadata)
 * receiveConfig — auth/events (přijímá do SQLite, validuje pathPrefix)
 *
 * Token musí být shodný na red i cílovém serveru.
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
            #
            # K čemu to je:
            #   Základní URL cílových serverů (events / auth), kam red modul posílá
            #   server-to-server requesty při sync static metadat a při načítání
            #   seznamu šablon do editoru. Bez nastavení se použije same-host fallback
            #   (aktuální request / HTTP_HOST) — vhodné pro vývoj na jednom serveru.
            #   V multi-server produkci odkomentujte a nastavte remote URL modulů.
            #
            # Kde se čte:
            #   - StaticRegistryPushClient::buildRegistryUrl()
            #       PUT/DELETE {base}{module}/v1/static/registry/{menuItemId}
            #   - StaticRegistryTemplateListClient::buildTemplatesUrl()
            #       GET {base}{module}/v1/static/templates?prefix=...
            #   Priorita base URL: parametr $baseUrl z requestu → moduleBaseUrls → HTTP_HOST.
            #
            # 'staticRegistry.push.moduleBaseUrls' => [
            #     'events' => 'https://events.example.com/app/',
            #     'auth'   => 'https://auth.example.com/app/',
            # ],
            #
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
