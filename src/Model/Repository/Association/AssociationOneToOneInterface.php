<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface AssociationOneToOneInterface extends AssociationInterface {

    /**
     * Vyzvedne entitu z potomkovského repository getByReference() a pomocí child hydratoru hydratuje rodičovskou entitu vyzvednutou potomkonskou entitou.
     * Poznámka: Pokud potomkovská entita neexistuje hydratuje hodnotou null.
     *
     * @param PersistableEntityInterface $parentEntity
     * @param RowDataInterface $parentRowData
     * @return void
     */
    public function recreateEntity(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void;

    public function addEntity(PersistableEntityInterface $parentEntity = null): void;

    public function removeEntity(PersistableEntityInterface $parentEntity = null): void;

}
