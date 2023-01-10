<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoInterface;

/**
 * Description of AssotiationAbstract
 *
 * @author pes2704
 */
abstract class OneAbstract implements AssociationInterface {

    /**
     * @var RepoInterface
     */
    protected $childRepo;  // zde je jen pro případ, že by nebyla definována v konkrétní asociaci

    /**
     *
     * @param RepoAssotiatedOneInterface $childRepo
     */
    public function __construct(RepoAssotiatedOneInterface $childRepo) {
        $this->childRepo = $childRepo;
    }

    /**
     * Pokud asociovaná (child) entita není persistována, přidá ji do repository.
     *
     * @param PersistableEntityInterface $childEntity
     * @return void
     */
    public function addEntity(PersistableEntityInterface $parentEntity=null): void {
        $this->childHydrator->extract($parentEntity, $rowData);
        $childEntity = $rowData[0];
        // parametr entity může být null - metoda je volána např. repo->addChild($parentEntity->getCosi())
        if (isset($childEntity) AND !$childEntity->isLocked() AND !$childEntity->isPersisted()) {
            $this->childRepo->add($childEntity);
        }
    }

    /**
     * Pokud asociovaná (child) entita je persistována, odstraní ji z repository.
     *
     * @param PersistableEntityInterface $childEntity
     * @return void
     */
    public function removeEntity(PersistableEntityInterface $parentEntity=null): void {
        $this->childHydrator->extract($parentEntity, $rowData);
        $childEntity = $rowData[0];
        // parametr entity může být null - metoda je volána např. repo->addChild($parentEntity->getCosi())
        if (isset($childEntity) AND !$childEntity->isLocked() AND $childEntity->isPersisted()) {
            $this->childRepo->remove($childEntity);
        }
    }

    /**
     *
     * @param iterable $parentEntities PersistableEntityInterface
     * @return void
     */
    protected function addEntities(iterable $parentEntities): void {
        foreach ($parentEntities as $parentEntity) {
            /** @var PersistableEntityInterface $parentEntity */
            if (!$parentEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            $this->addChild($parentEntity);
        }
    }

    protected function removeEntities(iterable $parentEntities): void {
        foreach ($parentEntities as $parentEntity) {
            /** @var PersistableEntityInterface $parentEntity */
            if (!$parentEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            $this->removeChild($parentEntity);
        }

    }

    public function flushChildRepo(): void {
        $this->childRepo->flush();
    }
}
