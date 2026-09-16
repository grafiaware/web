<?php

namespace Red\Service\StaticRegistry;

/**
 * Klient pro načtení seznamu záznamů z remote SQLite registry (events/auth).
 *
 * GET /{apiModule}/v1/static/registry?siteCode=...
 */
interface StaticRegistryListClientInterface {

    /**
     * @return array{items: list<array<string, mixed>>, count: int, error?: string}
     */
    public function fetch(string $apiModule, ?string $baseUrl = null): array;
}
