<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\HydratorInterface;

use Model\Repository\RepoAssotiatedOneInterface;
use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;

/**
 * Description of AssotiatedRepo
 *
 * @author pes2704
 */
abstract class AssociationOneToOne extends AssociationAbstract implements AssociationOneToOneInterface {

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
     * @param string $referenceName
     * @param RepoAssotiatedOneInterface $childRepo
     */
    public function __construct(string $referenceName, RepoAssotiatedOneInterface $childRepo) {
        parent::__construct($referenceName);
        $this->childRepo = $childRepo;
    }

    /**
     *
     * @param RowDataInterface $parentRowData
     * @return PersistableEntityInterface|null
     */
    protected function getChild(RowDataInterface $parentRowData): ?PersistableEntityInterface {
        return $this->childRepo->getByReference($this->referenceName, $parentRowData->getArrayCopy());
    }

    /**
     * Pokud entita není již persistována, přidá ji do repository.
     *
     * @param PersistableEntityInterface $entity
     * @return void
     */
    protected function addChild(PersistableEntityInterface $entity): void {
        if (!$entity->isLocked() AND !$entity->isPersisted()) {
            $this->childRepo->add($entity);
        }
    }
    /**
     * Pokud entita je persistována, odstraní ji z repository.
     *
     * @param PersistableEntityInterface $entity
     * @return void
     */
    protected function removeChild(PersistableEntityInterface $entity): void {
        if (!$entity->isLocked() AND $entity->isPersisted()) {
            $this->childRepo->remove($entity);
        }
    }
}