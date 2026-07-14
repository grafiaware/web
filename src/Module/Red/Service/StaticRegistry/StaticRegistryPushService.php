<?php

namespace Red\Service\StaticRegistry;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Repository\MenuItemRepo;
use Red\Service\ItemCreator\Enum\ItemApiGeneratorEnum;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Site\ConfigurationCache;

class StaticRegistryPushService {

    public function __construct(
        private StaticRegistryPushClientInterface $pushClient,
        private MenuItemRepo $menuItemRepo,
    ) {
    }

    public function pushForStaticItem(MenuItemInterface $menuItem, \Red\Model\Entity\StaticItemInterface $static, ?string $baseUrl = null): void {
        $apiModule = $menuItem->getApiModuleFk();
        if (!in_array($apiModule, ['events', 'auth'], true)) {
            return;
        }
        $this->pushClient->push(
            $apiModule,
            $static,
            ConfigurationCache::staticRegistry()['siteCode'],
            $baseUrl
        );
    }

    public function deleteForMenuItem(MenuItemInterface $menuItem, ?string $baseUrl = null): void {
        $apiModule = $menuItem->getApiModuleFk();
        if (!in_array($apiModule, ['events', 'auth'], true)) {
            return;
        }
        if ($menuItem->getApiGeneratorFk() !== ItemApiGeneratorEnum::STATIC_GENERATOR) {
            return;
        }
        $this->pushClient->delete($apiModule, (int) $menuItem->getId(), $baseUrl);
    }

    /**
     * @param array<int, array<string, mixed>> $subTreeRows
     */
    public function deleteForSubTreeRows(array $subTreeRows, ?string $baseUrl = null): void {
        foreach ($subTreeRows as $row) {
            $apiModule = $row['api_module_fk'] ?? null;
            $apiGenerator = $row['api_generator_fk'] ?? null;
            if (!in_array($apiModule, ['events', 'auth'], true)) {
                continue;
            }
            if ($apiGenerator !== ItemApiGeneratorEnum::STATIC_GENERATOR) {
                continue;
            }
            try {
                $this->pushClient->delete($apiModule, (int) $row['id'], $baseUrl);
            } catch (StaticRegistryPushException) {
            }
        }
    }
}
