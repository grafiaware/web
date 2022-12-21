<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface HydratorInterface {

    /**
     *
     * @param PersistableEntityInterface $entity
     * @param RowDataInterface $rowData
     */
    public function hydrate(PersistableEntityInterface $entity, RowDataInterface $rowData);

    /**
     *
     * @param PersistableEntityInterface $entity
     * @param RowDataInterface $rowData
     */
    public function extract(PersistableEntityInterface $entity, RowDataInterface $rowData);
}
