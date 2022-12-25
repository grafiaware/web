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
    public function hydrateByAssociatedEntity(PersistableEntityInterface $entity, RowDataInterface $parentRowData): void;
}
