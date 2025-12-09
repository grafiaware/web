<?php

namespace Red\Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;
use Red\Model\Entity\StaticItemInterface;

/**
 *
 * @author pes2704
 */
interface StaticItemRepoInterface extends RepoAssotiatedOneInterface {
    public function get($id): ?StaticItemInterface;
    public function getByMenuItemId($menuItemId): ?StaticItemInterface;
    public function add(StaticItemInterface $paper);
    public function remove(StaticItemInterface $paper);
}
