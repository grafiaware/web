<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface AssociationOneToOneInterface extends AssociationInterface {
    public function getAssociatedEntity(&$row): ?EntityInterface;
    public function addAssociatedEntity(EntityInterface $entity = null);
    public function removeAssociatedEntity(EntityInterface $entity = null);
}
