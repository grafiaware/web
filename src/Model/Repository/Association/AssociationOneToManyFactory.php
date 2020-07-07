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
class AssociationOneToManyFactory implements AssociationFactoryInterface {

    private $parentPropertyName;
    private $parentIdName;
    private $childRepo;


    public function __construct($parentPropertyName, $parentIdName, RepoAssotiatedManyInterface $childRepo) {
        $this->parentPropertyName = $parentPropertyName;
        $this->parentIdName = $parentIdName;
        $this->childRepo = $childRepo;
    }

    public function getParentPropertyName() {
        return $this->parentPropertyName;
    }

    public function getChildRepo(): RepoInterface {
        return $this->childRepo;
    }

    public function getParentIdName() {
        return $this->parentIdName;
    }

    public function createAssociated(&$row) {
        $row[$this->getParentPropertyName()] = $this->childRepo->findByReference($row[$this->getParentIdName()]);
    }
}
