<?php

namespace Red\Service\StaticRegistry;

/**
 * Klient pro načtení seznamu static šablon z remote modulu (events/auth).
 */
interface StaticRegistryTemplateListClientInterface {

    /**
     * @return array<int, array{path: string, template: string, fullTemplatePath?: string}>
     */
    public function fetch(string $apiModule, string $prefix, ?string $baseUrl = null): array;
}
