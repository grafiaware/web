<?php

namespace Red\Service\StaticRegistry;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Repository\MenuItemRepo;
use Red\Service\ItemCreator\Enum\ItemApiGeneratorEnum;
use Red\Service\StaticRegistry\Exception\StaticRegistryPushException;
use Site\ConfigurationCache;

/**
 * Doménová vrstva nad StaticRegistryPushClient — filtruje podle api_module_fk.
 *
 * Volá se z StaticControler (update path/template), StaticItemCreator (nová položka)
 * a HierarchyControler (delete subtree).
 *
 * @author pes2704
 */
class StaticRegistryPushService {

    public function __construct(
        private StaticRegistryPushClientInterface $pushClient,
        private MenuItemRepo $menuItemRepo,
    ) {
    }

    /**
     * Push jen pokud menu item patří do events|auth static (red static zůstává lokálně).
     */
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
     * Hromadné DELETE před smazáním hierarchy subtree (CASCADE v red DB by registry na remote neaktualizovalo).
     *
     * @param array<int, array<string, mixed>> $subTreeRows řádky z HierarchyAggregateReadonlyDao::getSubTree()
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
                // pokračovat se smazáním dalších položek — lokální delete v red DB má prioritu
            }
        }
    }
}
