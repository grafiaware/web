<?php

namespace Red\Service\StaticRegistry;

use Red\Model\Entity\StaticItemInterface;

/**
 * Kontrakt HTTP klienta pro synchronizaci static metadat z red na auth/events.
 */
interface StaticRegistryPushClientInterface {

    /**
     * Pushne (upsert) metadata StaticItem na cílový modul.
     *
     * @param string $apiModule events|auth
     * @param string|null $baseUrl Override base URL (request host); null = konfigurace / same-host
     */
    public function push(string $apiModule, StaticItemInterface $static, string $siteCode, ?string $baseUrl = null): void;

    /**
     * Smaže záznam v registry cílového modulu.
     */
    public function delete(string $apiModule, int $menuItemId, ?string $baseUrl = null): void;
}
