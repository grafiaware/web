<?php

namespace Red\Service\StaticRegistry;

use Site\ConfigurationCache;

/**
 * Načte seznam záznamů z remote SQLite registry (auth/events) pro admin přehled.
 *
 * GET /{apiModule}/v1/static/registry?siteCode=...
 * Při selhání vrací prázdné items + error — šablona může zobrazit hlášku.
 *
 * @author pes2704
 */
class StaticRegistryListClient implements StaticRegistryListClientInterface {

    /**
     * {@inheritdoc}
     */
    public function fetch(string $apiModule, ?string $baseUrl = null): array {
        $url = $this->buildListUrl($apiModule, $baseUrl);
        $token = ConfigurationCache::staticRegistry()['staticRegistry.token'] ?? '';
        $headers = "X-Static-Registry-Token: $token\r\n";
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => $headers,
                'ignore_errors' => true,
                'timeout' => 10,
            ],
        ];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            return ['items' => [], 'count' => 0, 'error' => "Nelze načíst registry z $url"];
        }
        if (isset($http_response_header[0]) && !str_contains($http_response_header[0], ' 200')) {
            return ['items' => [], 'count' => 0, 'error' => "Registry $apiModule: {$http_response_header[0]}"];
        }
        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            return ['items' => [], 'count' => 0, 'error' => "Neplatná JSON odpověď z $apiModule registry"];
        }
        $items = $decoded['items'] ?? [];
        if (!is_array($items)) {
            $items = [];
        }
        return [
            'items' => array_values($items),
            'count' => (int) ($decoded['count'] ?? count($items)),
            'error' => isset($decoded['error']) ? (string) $decoded['error'] : null,
        ];
    }

    private function buildListUrl(string $apiModule, ?string $baseUrl): string {
        $configuredBase = ConfigurationCache::staticRegistry()['staticRegistry.push.moduleBaseUrls'][$apiModule] ?? null;
        if ($baseUrl !== null && $baseUrl !== '') {
            $root = rtrim($baseUrl, '/') . '/';
        } elseif (is_string($configuredBase) && $configuredBase !== '') {
            $root = rtrim($configuredBase, '/') . '/';
        } else {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $root = "$scheme://$host/";
        }
        $siteCode = urlencode((string) (ConfigurationCache::staticRegistry()['staticRegistry.siteCode'] ?? ''));
        return $root . "$apiModule/v1/static/registry?siteCode=$siteCode";
    }
}
