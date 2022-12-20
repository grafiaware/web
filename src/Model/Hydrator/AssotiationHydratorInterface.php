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
interface AssotiationHydratorInterface {

    /**
     *
     * @param EntityInterface $entity
     * @param EntityInterface $assotiatedEntity
     */
    public function hydrate(EntityInterface $entity, EntityInterface $assotiatedEntity);

    /**
     *
     * @param EntityInterface $entity
     * @param EntityInterface $assotiatedEntity
     */
    public function extract(EntityInterface $entity, EntityInterface $assotiatedEntity);
}
