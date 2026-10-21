<?php

namespace Red\Service\StaticRegistry;

use Red\Model\Entity\StaticItemInterface;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Site\ConfigurationCache;

/**
 * HTTP klient pro push/delete static metadat z red serveru na auth/events registry.
 *
 * Pattern server-to-server volání stejný jako SynchroControler (file_get_contents + stream_context).
 * Token v hlavičce X-Static-Registry-Token musí sedět s tokenem na cílovém modulu.
 *
 * @author pes2704
 */
class StaticRegistryPushClient implements StaticRegistryPushClientInterface {

    /**
     * {@inheritdoc}
     *
     * PUT /{apiModule}/v1/static/registry/{menuItemId}
     */
    public function push(string $apiModule, StaticItemInterface $static, string $siteCode, ?string $baseUrl = null): void {
        if (!$this->isPushEnabled() || !$this->isRemoteModule($apiModule)) {
            return; // red|static zůstává jen v red DB, push není potřeba
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
     *
     * DELETE /{apiModule}/v1/static/registry/{menuItemId}
     */
    public function delete(string $apiModule, int $menuItemId, ?string $baseUrl = null): void {
        if (!$this->isPushEnabled() || !$this->isRemoteModule($apiModule)) {
            return;
        }
        $url = $this->buildRegistryUrl($apiModule, $menuItemId, $baseUrl);
        $this->httpRequest('DELETE', $url, null);
    }

    private function isPushEnabled(): bool {
        return (bool) (ConfigurationCache::staticRegistry()['staticRegistry.push.enabled'] ?? false);
    }

    /** Push má smysl jen pro moduly bez red DB (events, auth). */
    private function isRemoteModule(string $apiModule): bool {
        return in_array($apiModule, ['events', 'auth'], true);
    }

    /**
     * Priorita base URL: explicitní parametr → konfigurace moduleBaseUrls → same-host fallback.
     * Prázdné / chybějící moduleBaseUrls = typicky vývoj na jednom hostu (red i events na stejném serveru).
     */
    private function buildRegistryUrl(string $apiModule, int $menuItemId, ?string $baseUrl): string {
        $root = StaticRegistryBaseUrlResolver::resolve($apiModule, $baseUrl);
        return $root . "$apiModule/v1/static/registry/$menuItemId";
    }

    private function httpRequest(string $method, string $url, ?string $body): void {
        $token = ConfigurationCache::staticRegistry()['staticRegistry.token'] ?? '';
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
        // $http_response_header je automatická proměnná PHP po file_get_contents přes HTTP
        if (isset($http_response_header[0]) && !str_contains($http_response_header[0], ' 200')
            && !str_contains($http_response_header[0], ' 204')
            && !str_contains($http_response_header[0], ' 201')) {
            throw new StaticRegistryPushException("Push na $url vrátil neočekávanou odpověď: {$http_response_header[0]}");
        }
    }
}
