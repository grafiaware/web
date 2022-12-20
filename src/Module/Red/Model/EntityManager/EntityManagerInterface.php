<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\EntityManager;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface EntityManagerInterface {
    public function registerHydrator($entityClass, RowHydratorInterface $hydrator);
    public function hydrate($entityClass, EntityInterface $entity, &$row);
    public function extract($entityClass, EntityInterface $entity, &$row);
    public function recreateEntity($entityClass, $index, $row): ?EntityInterface;
    public function flush();
    public function __destruct();

}
