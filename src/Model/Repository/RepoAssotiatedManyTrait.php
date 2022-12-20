<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;

/**
 * Description of RepoAggregateAbstract
 *
 * @author pes2704
 */
trait RepoAssotiatedManyTrait {

    /**
     *
     * @param string $parentTableName
     * @param ...$referenceParams
     * @return iterable
     */
    public function findByReference(string $parentTableName, ...$referenceParams): iterable {
        return $this->addEntitiesByRowDataArray($this->dataManager->findByFk($parentTableName, $referenceParams));
    }
}
