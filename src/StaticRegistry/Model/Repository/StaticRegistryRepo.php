<?php

namespace StaticRegistry\Model\Repository;

use DateTime;
use Red\Model\Entity\StaticItemClass;
use Red\Model\Entity\StaticItemInterface;
use StaticRegistry\Model\Entity\StaticRegistryEntry;
use StaticRegistry\Model\Storage\StaticRegistryStorage;

/**
 * {@inheritdoc}
 *
 * @author pes2704
 */
class StaticRegistryRepo implements StaticRegistryRepoInterface {

    public function __construct(
        private StaticRegistryStorage $storage,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function upsert(StaticRegistryEntry $entry): bool {
        return $this->storage->upsert($entry);
    }

    public function delete(int $menuItemId): void {
        $this->storage->delete($menuItemId);
    }

    public function getByMenuItemId(int $menuItemId): ?StaticRegistryEntry {
        return $this->storage->getByMenuItemId($menuItemId);
    }

    /**
     * {@inheritdoc}
     *
     * redStaticId mapuje na StaticItem.id — editor v red pak ví, kam POSTovat update path/template.
     */
    public function toStaticItemInterface(StaticRegistryEntry $entry): StaticItemInterface {
        $static = new StaticItemClass();
        $static->setId($entry->getRedStaticId());
        $static->setMenuItemIdFk($entry->getMenuItemId());
        $static->setPath($entry->getPath());
        $static->setTemplate($entry->getTemplate());
        $static->setCreator($entry->getCreator());
        try {
            $static->setUpdated(new DateTime($entry->getUpdated()));
        } catch (\Exception) {
            $static->setUpdated(null);
        }
        return $static;
    }
}
