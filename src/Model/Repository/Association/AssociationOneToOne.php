<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Repository\RepoAssotiatedOneInterface;
use Model\Repository\RepoInterface;
use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;

/**
 * Description of AssotiatedRepo
 *
 * @author pes2704
 */
class AssociationOneToOne extends AssociationAbstract implements AssociationInterface {

    /**
     * @var RepoAssotiatedOneInterface
     */
    private $childRepo;

    private $entities;

    /**
     *
     * @param string $parentPropertyName Jméno vlastnosti rodičovské enity, která bude obsahovat asociovanou potomkovskou entitu
     * @param array $parentReferenceKeyAttribute Jméno vlatnosti rodičovské enzity, která obsahuje referenční cizí klíč
     * @param RepoAssotiatedOneInterface $childRepo Repo pro získání asociovaných entit
     */
    public function __construct($parentPropertyName, $parentReferenceKeyAttribute, RepoAssotiatedOneInterface $childRepo) {
        parent::__construct($parentPropertyName, $parentReferenceKeyAttribute);
        $this->childRepo = $childRepo;
    }

    public function getAssociated(&$row) {
        $childKey = $this->getChildKey($row);
        $index = $this->indexFromKey($childKey);
        if (!isset($this->entities[$index])) {
            $child = $this->childRepo->getByReference($childKey);
            if (is_null($child)) {
                throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociovanou entitu pro vlastnost rodiče '$this->parentPropertyName'. Nebyla načtena entita.");
            }
            $this->entities[$index] = $child;
        }
        $row[$this->parentPropertyName] = $this->entities[$index];
    }

    public function getChildRepo(): RepoInterface {
        return $this->childRepo;
    }

//    public function getParentReferenceKeyAttribute() {
//        return $this->parentReferenceKeyAttribute;
//    }

//    public function getParentPropertyName() {
//        return $this->parentPropertyName;
//    }

}
