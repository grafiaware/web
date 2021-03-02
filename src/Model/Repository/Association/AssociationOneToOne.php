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

    /**
     *
     * @param array $parentReferenceKeyAttribute Atribut klíče, který je referencí na data rodiče v úložišti dat. V databázi jde o referenční cizí klíč.
     * @param RepoAssotiatedOneInterface $childRepo Repo pro získání, ukládání a mazání asociovaných entit
     */
    public function __construct($referenceKeyAttribute, RepoAssotiatedOneInterface $childRepo) {
        parent::__construct($referenceKeyAttribute);
        $this->childRepo = $childRepo;
    }

    public function getAssociated(&$row) {
        $childKey = $this->getChildKey($row);
        $child = $this->childRepo->getByReference($childKey);
        if (is_null($child)) {
            $repoCls = get_class($this->childRepo);
            throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociovanou entitu. Nebyla načtena entita z repository asociovaných entit $repoCls.");
        }
        return $child;
    }
    public function addAssociated($entity) {
        $this->childRepo->add($entity);   //TODO: repo interface - add, remove
    }

    public function removeAssociated($entty) {
        $this->childRepo->remove($entty);
    }
}
