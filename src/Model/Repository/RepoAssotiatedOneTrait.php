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
     * @param string $referenceName
     * @param array $parentRowData parent data
     * @return PersistableEntityInterface|null
     */
    public function getByReference(string $referenceName, array $parentRowData): ?PersistableEntityInterface {
        // vždy čte data - neví jestli jsou v $this->data
        $childRowData = $this->dataManager->getByReference($referenceName, $parentRowData);
        return $this->recreateEntityByRowData($childRowData);
    }
}
