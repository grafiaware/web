<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\AssotiationHydratorInterface;

use Model\Repository\RepoAssotiatedOneInterface;
use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;

/**
 * Description of AssotiatedRepo
 *
 * @author pes2704
 */
class AssociationOneToOne extends AssociationAbstract implements AssociationOneToOneInterface {

    /**
     * @var RepoAssotiatedOneInterface
     */
    protected $childRepo;


    /**
     *
     * @param array $childKeyAttribute Atribut cizího klíče, klíče který je referencí na data rodiče v úložišti dat. V databázi jde o jméno sloupce (sloupců) s cizím klíčem v potomkovské tabulce.
     * @param array $parentKeyAttribute Atribut vlastního klíče, klíče na který vede reference na data rodiče v úložišti dat. V databázi jde o jméno sloupce (sloupců) s vlastním (obvykle primárním)  klíčem v potomkovské tabulce.
     * @param RepoAssotiatedOneInterface $childRepo Repo pro získání, ukládání a mazání asociované entity.
     */
    /**
     *
     * @param string $parentTable
     * @param RepoAssotiatedOneInterface $childRepo
     */
    public function __construct(string $parentTable, RepoAssotiatedOneInterface $childRepo, AssotiationHydratorInterface $childHydrator) {
        parent::__construct($parentTable, $childHydrator);
        $this->childRepo = $childRepo;
    }

    public function hydrateByAssociatedEntity(EntityInterface $entity, RowDataInterface $parentRowData): void {
        $child = $this->childRepo->getByReference($this->parentTable, $parentRowData->getArrayCopy());

//        if (is_null($child)) {
//            $repoCls = get_class($this->childRepo);
//            throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociovanou entitu. Nebyla načtena entita z repository asociovaných entit $repoCls.");
//        }
        $this->childHydrator->hydrate($entity, $child);
    }
}
