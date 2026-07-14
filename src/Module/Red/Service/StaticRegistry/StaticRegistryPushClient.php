<?php

namespace Red\Service\StaticRegistry;

use Red\Model\Entity\StaticItemInterface;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Site\ConfigurationCache;
use StaticRegistry\Exception\StaticRegistryException;

class StaticRegistryPushClient implements StaticRegistryPushClientInterface {

    /**
     * {@inheritdoc}
     */
    public function push(string $apiModule, StaticItemInterface $static, string $siteCode, ?string $baseUrl = null): void {
        if (!$this->isPushEnabled() || !$this->isRemoteModule($apiModule)) {
            return;
        }
        $url = $this->buildRegistryUrl($apiModule, (int) $static->getMenuItemIdFk(), $baseUrl);
        $body = json_encode([
            'redStaticId' => (int) ($static->getId() ?? 0),
            'path' => $static->getPath() ?? '',
            'template' => $static->getTemplate() ?? '',
            'creator' => $static->getCreator(),
            'updated' => $static->getUpdated()?->format(DATE_ATOM) ?? date(DATE_ATOM),
            'siteCode' => $siteCode,
        ]);
        if ($body === false) {
            throw new StaticRegistryPushException('Nelze serializovat static metadata pro push.');
        }
        $this->httpRequest('PUT', $url, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $apiModule, int $menuItemId, ?string $baseUrl = null): void {
        if (!$this->isPushEnabled() || !$this->isRemoteModule($apiModule)) {
            return;
        }
        $url = $this->buildRegistryUrl($apiModule, $menuItemId, $baseUrl);
        $this->httpRequest('DELETE', $url, null);
    }

    private function isPushEnabled(): bool {
        return (bool) (ConfigurationCache::staticRegistry()['push']['enabled'] ?? false);
    }

    private function isRemoteModule(string $apiModule): bool {
        return in_array($apiModule, ['events', 'auth'], true);
    }

    private function buildRegistryUrl(string $apiModule, int $menuItemId, ?string $baseUrl): string {
        $configuredBase = ConfigurationCache::staticRegistry()['push']['moduleBaseUrls'][$apiModule] ?? null;
        if ($baseUrl !== null && $baseUrl !== '') {
            $root = rtrim($baseUrl, '/') . '/';
        } elseif (is_string($configuredBase) && $configuredBase !== '') {
            $root = rtrim($configuredBase, '/') . '/';
        } else {
            $root = $this->resolveSameHostBaseUrl();
        }
        return $root . "$apiModule/v1/static/registry/$menuItemId";
    }

    private function resolveSameHostBaseUrl(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "$scheme://$host/";
    }

    private function httpRequest(string $method, string $url, ?string $body): void {
        $token = ConfigurationCache::staticRegistry()['token'] ?? '';
        $headers = "Content-Type: application/json\r\n"
            . "X-Static-Registry-Token: $token\r\n";
        $options = [
            'http' => [
                'method' => $method,
                'header' => $headers,
                'content' => $body ?? '',
                'ignore_errors' => true,
                'timeout' => 10,
            ],
        ];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            throw new StaticRegistryPushException("Push na $url selhal.");
        }
        if (isset($http_response_header[0]) && !str_contains($http_response_header[0], ' 200')
            && !str_contains($http_response_header[0], ' 204')
            && !str_contains($http_response_header[0], ' 201')) {
            throw new StaticRegistryPushException("Push na $url vrátil neočekávanou odpověď: {$http_response_header[0]}");
        }
    }
}
