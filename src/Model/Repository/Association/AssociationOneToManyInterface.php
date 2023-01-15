<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\RowData\RowDataInterface;
use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface AssociationOneToManyInterface extends AssociationInterface {

   /**
    *
    * @param PersistableEntityInterface[] $parentEntities
    * @param RowDataInterface[] $parentRowdaraArray
    * @return void
    */
    public function recreateChildEntities(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void;

    public function addEntities(PersistableEntityInterface $parentEntity): void;

    public function removeEntities(PersistableEntityInterface $parentEntity): void;
}
