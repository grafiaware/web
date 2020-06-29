<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Repository\RepoAssotiatedOneInterface;

use Model\Entity\EntityInterface;

/**
 * Description of AssotiatedRepo
 *
 * @author pes2704
 */
class AssociationOneToOneFactory implements AssociationFactoryInterface {

    private $parentPropertyName;
    private $parentIdName;

    private $entities;

    /**
     *
     * @var RepoAssotiatedOneInterface
     */
    private $childRepo;


    public function __construct($parentPropertyName, $parentIdName, RepoAssotiatedOneInterface $childRepo) {
        $this->parentPropertyName = $parentPropertyName;
        $this->parentIdName = $parentIdName;
        $this->childRepo = $childRepo;
    }

    public function createAssociated(&$row) {
        $childId = $row[$this->getParentIdName()];
        if (!isset($this->entities[$childId])) {
            $this->entities[$childId] = $this->childRepo->getByReference($childId);
        }
        $row[$this->getParentPropertyName()] = $this->entities[$childId];
    }

    public function getParentIdName() {
        return $this->parentIdName;
    }

    public function getParentPropertyName() {
        return $this->parentPropertyName;
    }
}
