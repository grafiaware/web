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
     * @param string $referenceName
     * @param RepoAssotiatedOneInterface $childRepo
     */
    public function __construct(RepoAssotiatedOneInterface $childRepo) {
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
    protected function addChild(PersistableEntityInterface $entity=null): void {
        // parametr entity může být null - metoda je volána např. repo->addChild($parentEntity->getCosi())
        if (isset($entity) AND !$entity->isLocked() AND !$entity->isPersisted()) {
            $this->childRepo->add($entity);
        }
    }

    /**
     * Pokud entita je persistována, odstraní ji z repository.
     *
     * @param PersistableEntityInterface $entity
     * @return void
     */
    protected function removeChild(PersistableEntityInterface $entity=null): void {
        // parametr entity může být null - metoda je volána např. repo->addChild($parentEntity->getCosi())
        if (isset($entity) AND !$entity->isLocked() AND $entity->isPersisted()) {
            $this->childRepo->remove($entity);
        }
    }
}