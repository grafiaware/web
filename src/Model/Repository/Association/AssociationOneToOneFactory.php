<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Repository\RepoAssotiatedOneInterface;
use Model\Repository\RepoInterface;
use Model\Repository\RepoPublishedOnlyModeInterface;
use Model\Entity\EntityInterface;

/**
 * Description of AssotiatedRepo
 *
 * @author pes2704
 */
class AssociationOneToOneFactory implements AssociationFactoryInterface {

    private $parentPropertyName;
    private $parentIdName;

    /**
     *
     * @var RepoAssotiatedOneInterface
     */
    private $childRepo;

    private $entities;

    public function __construct($parentPropertyName, $parentIdName, RepoAssotiatedOneInterface $childRepo) {
        $this->parentPropertyName = $parentPropertyName;
        $this->parentIdName = $parentIdName;
        $this->childRepo = $childRepo;
    }

    public function createAssociated(&$row) {
        $parentKeyAttribute = $this->getParentIdName();
        if (is_array($parentKeyAttribute)) {
            foreach ($parentKeyAttribute as $field) {
                $childKey[$field] = $row[$field];
            }
        } else {
            $childKey = $row[$this->getParentIdName()];
        }


        $index = $this->indexFromId($childKey);
        if (!isset($this->entities[$index])) {
            $this->entities[$index] = $this->childRepo->getByReference($childKey);
        }
        $row[$this->getParentPropertyName()] = $this->entities[$index];
    }

    public function getChildRepo(): RepoInterface {
        return $this->childRepo;
    }

    public function getParentIdName() {
        return $this->parentIdName;
    }

    public function getParentPropertyName() {
        return $this->parentPropertyName;
    }

    protected function indexFromId($id) {
        if (is_array($id)) {
            return implode($id);
        } else{
            return $id;
        }
    }
}
