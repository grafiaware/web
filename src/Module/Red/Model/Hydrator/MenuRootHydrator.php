<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
use ArrayAccess;

use Red\Model\Entity\MenuRootInterface;

/**
 * Description of MenuItemTypeHydrator
 *
 * @author pes2704
 */
class MenuRootHydrator implements HydratorInterface {
    /**
     *
     * @param MenuRootInterface $menuRoot
     * @param type $row
     */
    public function hydrate(EntityInterface $menuRoot, ArrayAccess $rowData) {
        /** @var MenuRootInterface $menuRoot */
        $menuRoot->setName($rowData->offsetGet('name'));
        $menuRoot->setUidFk($rowData->offsetGet('uid_fk'));
    }

    /**
     *
     * @param MenuRootInterface $menuRoot
     * @param array $rowData
     */
    public function extract(EntityInterface $menuRoot, ArrayAccess $rowData) {
        /** @var MenuRootInterface $menuRoot */
        $rowData->offsetSet('name',  $menuRoot->getName());
        $rowData->offsetSet('uid_fk',  $menuRoot->getUidFk());
    }
}
