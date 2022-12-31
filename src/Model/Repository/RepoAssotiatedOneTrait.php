<?php
namespace Model\Repository;

use Model\Entity\PersistableEntityInterface;

use Model\Repository\RepoAssotiatedOneInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatedOneInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoAssotiatedOneTrait {

    /** @var RepoAbstract $this */

    /**
     *
     * @param string $parentTableName
     * @param array $parentRowData parent data
     * @return PersistableEntityInterface|null
     */
    public function getByReference(string $parentTableName, array $parentRowData): ?PersistableEntityInterface {
        // vždy čte data - neví jestli jsou v $this->data
        $childRowData = $this->dataManager->getByReference($parentTableName, $parentRowData);
        return $this->recreateEntityByRowData($childRowData);
    }
}
