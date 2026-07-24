<?php

namespace StaticRegistry\Model\Repository;

use Red\Model\Entity\StaticItemInterface;
use StaticRegistry\Model\Entity\StaticRegistryEntry;

/**
 * Repository lokální static registry (SQLite na auth/events).
 *
 * Oddělená od StaticItemRepo v red DB — auth/events red databázi nepřipojují.
 */
interface StaticRegistryRepoInterface {

    /**
     * Upsert = insert nebo update podle menu_item_id.
     *
     * @return bool true pokud byl záznam zapsán, false pokud byl přeskočen (starší updated)
     */
    public function upsert(StaticRegistryEntry $entry): bool;

    public function delete(int $menuItemId): void;

    public function getByMenuItemId(int $menuItemId): ?StaticRegistryEntry;

    /**
     * Převod lokálního záznamu na StaticItemInterface — StaticItemViewModel
     * pak nemusí rozlišovat zdroj (red DB vs. lokální registry).
     */
    public function toStaticItemInterface(StaticRegistryEntry $entry): StaticItemInterface;
}
