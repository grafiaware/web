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

    /**
     *
     * @param type $parentReferenceKeyAttribute
     * @param RepoAssotiatedManyInterface $childRepo
     */
    public function __construct($parentReferenceKeyAttribute, RepoAssotiatedManyInterface $childRepo) {
        parent::__construct($parentReferenceKeyAttribute);
        $this->childRepo = $childRepo;
    }

    public function getAssociated(&$row) {
        $childKey = $this->getChildKey($row);
        $children = $this->childRepo->findByReference($childKey);
//        if (!$children) {
//            throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociované entity pro vlastnost rodiče '$this->parentPropertyName'. Nebyla načtena entita.");
//        }
        return $children;
    }

    public function addAssociated($entity) {
        $this->childRepo->add($entity);
    }

    public function removeAssociated($entty) {
        $this->childRepo->remove($entty);
    }
}