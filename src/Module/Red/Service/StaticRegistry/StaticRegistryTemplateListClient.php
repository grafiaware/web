<?php

namespace Red\Service\StaticRegistry;

use Site\ConfigurationCache;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;

class StaticRegistryTemplateListClient implements StaticRegistryTemplateListClientInterface {

    /**
     * {@inheritdoc}
     */
    public function fetch(string $apiModule, string $prefix, ?string $baseUrl = null): array {
        $url = $this->buildTemplatesUrl($apiModule, $prefix, $baseUrl);
        $token = ConfigurationCache::staticRegistry()['token'] ?? '';
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
            return [];
        }
        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            return [];
        }
        return $decoded['templates'] ?? [];
    }

    private function buildTemplatesUrl(string $apiModule, string $prefix, ?string $baseUrl): string {
        $configuredBase = ConfigurationCache::staticRegistry()['push']['moduleBaseUrls'][$apiModule] ?? null;
        if ($baseUrl !== null && $baseUrl !== '') {
            $root = rtrim($baseUrl, '/') . '/';
        } elseif (is_string($configuredBase) && $configuredBase !== '') {
            $root = rtrim($configuredBase, '/') . '/';
        } else {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $root = "$scheme://$host/";
        }
        $siteCode = urlencode(ConfigurationCache::staticRegistry()['siteCode']);
        $encodedPrefix = urlencode($prefix);
        return $root . "$apiModule/v1/static/templates?prefix=$encodedPrefix&siteCode=$siteCode";
    }
}
