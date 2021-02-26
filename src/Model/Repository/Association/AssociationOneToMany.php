<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Repository\RepoAssotiatedManyInterface;
use Model\Repository\RepoInterface;

/**
 * Description of AssociationOneToManyFactory
 *
 * @author pes2704
 */
class AssociationOneToMany extends AssociationAbstract implements AssociationInterface {

    /**
     * @var RepoAssotiatedManyInterface
     */
    private $childRepo;

    public function __construct($parentPropertyName, $parentIdName, RepoAssotiatedManyInterface $childRepo) {
        parent::__construct($parentPropertyName, $parentReferenceKeyAttribute);
        $this->childRepo = $childRepo;
    }

//    public function getParentPropertyName() {
//        return $this->parentPropertyName;
//    }

    public function getChildRepo(): RepoInterface {
        return $this->childRepo;
    }

//    public function getParentReferenceKeyAttribute() {
//        return $this->parentReferenceKeyAttribute;
//    }

    public function getAssociated(&$row) {
        $childKey = $this->getChildKey($row);
        $index = $this->indexFromKey($childKey);
        if (!isset($this->entities[$index])) {
            $children = $this->childRepo->findByReference($row[$this->parentReferenceKeyAttribute]);
            if (!$children) {
                throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociované entity pro vlastnost rodiče '$this->parentPropertyName'. Nebyla načtena entita.");
            }
            $this->entities[$index] = $children;
        }
        $row[$this->parentPropertyName] = $this->entities[$index];
    }
}
