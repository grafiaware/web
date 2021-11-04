<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface HydratorInterface {

    /**
     *
     * @param EntityInterface $entity
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $entity, RowDataInterface $rowData);

    /**
     *
     * @param EntityInterface $entity
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $entity, RowDataInterface $rowData);
}
