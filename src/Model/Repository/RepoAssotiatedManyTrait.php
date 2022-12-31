<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

use Model\Repository\RepoAssotiatedManyInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatedManyInterface interface pro POTOMKOVSKÉ repository s asociací 1:N
 *
 * @author pes2704
 */
trait RepoAssotiatedManyTrait {

    /**
     *
     * @param string $referenceName Jméno reference definované v DAO (obvykle jméno rodičovské tabulky)
     * @param ...$referenceParams
     * @return iterable
     */
    public function findByReference(string $referenceName, ...$referenceParams): iterable {
        return $this->recreateEntitiesByRowDataArray($this->dataManager->findByReference($referenceName, $referenceParams));
    }
}
