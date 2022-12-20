<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Repository;

use Model\Entity\EntityInterface;

/**
 * Description of RepoAggregateAbstract
 *
 * @author pes2704
 */
trait RepoAssotiatedOneTrait {

    /**
     *
     * @param string $parentTableName
     * @param array $parentRowData parent data
     * @return EntityInterface|null
     */
    public function getByReference(string $parentTableName, array $parentRowData): ?EntityInterface {
        // vždy čte data - neví jestli jsou v $this->data
        $childRowData = $this->dataManager->getByReference($parentTableName, $parentRowData);
        return $this->addEntityByRowData($childRowData);
    }
}
