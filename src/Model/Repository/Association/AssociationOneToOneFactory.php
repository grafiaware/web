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
class AssociationOneToOneFactory implements AssociationFactoryInterface {

    private $parentPropertyName;
    private $parentReferenceKeyAttribute;

    /**
     *
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
        $this->parentPropertyName = $parentPropertyName;
        $this->parentReferenceKeyAttribute = $parentReferenceKeyAttribute;
        $this->childRepo = $childRepo;
    }

    public function createAssociated(&$row) {
        $parentKeyAttribute = $this->getParentIdName();
        if (is_array($parentKeyAttribute)) {
            foreach ($parentKeyAttribute as $field) {
                if( ! array_key_exists($field, $row)) {
                    throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociovanou entitu pro vlastnost rodiče {$this->parentPropertyName}. Atribut referenčního klíče obsahuje pole $field a pole řádku dat pro vytvoření potomkovské entity neobsahuje takový prvek.");
                }
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
        return $this->parentReferenceKeyAttribute;
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
