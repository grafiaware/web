<?php

namespace StaticRegistry\Model\Repository;

use Red\Model\Entity\StaticItemInterface;
use StaticRegistry\Model\Entity\StaticRegistryEntry;

interface StaticRegistryRepoInterface {

    public function upsert(StaticRegistryEntry $entry): bool;

    public function delete(int $menuItemId): void;

    public function getByMenuItemId(int $menuItemId): ?StaticRegistryEntry;

    public function toStaticItemInterface(StaticRegistryEntry $entry): StaticItemInterface;
}
