<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\EntityManager;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface EntityManagerInterface {
    public function registerHydrator($entityClass, HydratorInterface $hydrator);
    public function hydrate($entityClass, PersistableEntityInterface $entity, &$row);
    public function extract($entityClass, PersistableEntityInterface $entity, &$row);
    public function recreateEntity($entityClass, $index, $row): ?PersistableEntityInterface;
    public function flush();
    public function __destruct();

}
