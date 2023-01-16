<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use ArrayAccess;

/**
 *
 * @author pes2704
 */
interface HydratorInterface {

    /**
     *
     * @param PersistableEntityInterface $entity
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $entity, ArrayAccess $rowData);

    /**
     *
     * @param PersistableEntityInterface $entity
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $entity, ArrayAccess $rowData);
}
