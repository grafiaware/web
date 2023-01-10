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

use Model\Repository\RepoJoinedOneInterface;
use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;

/**
 * Description of AssotiatedRepo
 *
 * @author pes2704
 */
abstract class JoinOneToOne extends AssociationOneToOne implements AssociationOneToOneInterface {

    /**
     * @var RepoJoinedOneInterface
     */
    protected $childRepo;

    /**
     *
     * @param RepoJoinedOneInterface $childRepo
     */
    public function __construct(RepoJoinedOneInterface $childRepo) {
        $this->childRepo = $childRepo;
    }

    /**
     * Vyzvedne entitu z potomkovského repository recreateEntityByParentData() a pomocí child hydratoru hydratuje rodičovskou entitu vyzvednutou potomkonskou entitou.
     * Data a hodnoty pro hydratac potomkovské entity metoda potomkovského repository->recreateEntityByParentData bere z rodičovských dat.
     * V potomkovském repository nedochází ke čtení dat z úložiště dat (databáze).
     *
     * @param RowDataInterface $parentRowData
     * @return PersistableEntityInterface
     */
    public function recreateChildEntity(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        $childEntity = $this->childRepo->recreateEntityByParentData($parentRowData);
        $this->hydrateChild($parentEntity, $childEntity);
    }

}