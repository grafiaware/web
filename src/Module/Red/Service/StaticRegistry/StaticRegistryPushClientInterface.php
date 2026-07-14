<?php

namespace Red\Service\StaticRegistry;

use Red\Model\Entity\StaticItemInterface;

interface StaticRegistryPushClientInterface {

    public function push(string $apiModule, StaticItemInterface $static, string $siteCode, ?string $baseUrl = null): void;

    public function delete(string $apiModule, int $menuItemId, ?string $baseUrl = null): void;
}
