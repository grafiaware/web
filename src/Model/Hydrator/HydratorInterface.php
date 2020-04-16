<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface HydratorInterface {
    
    /**
     *
     * @param EntityInterface $entity
     * @param array $row
     */
    public function hydrate(EntityInterface $entity, &$row);

    /**
     *
     * @param EntityInterface $entity
     * @param array $row
     */
    public function extract(EntityInterface $entity, &$row);
}
