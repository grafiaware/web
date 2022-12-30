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

use Red\Model\Entity\MenuItemTypeInterface;

/**
 * Description of MenuItemTypeHydrator
 *
 * @author pes2704
 */
class MenuItemTypeHydrator implements HydratorInterface {
    /**
     *
     * @param MenuItemTypeInterface $menuItemType
     * @param type $row
     */
    public function hydrate(EntityInterface $menuItemType, ArrayAccess $rowData) {
        /** @var MenuItemTypeInterface $menuItemType */
        $menuItemType->setType($rowData->offsetGet('type'));
    }

    /**
     *
     * @param MenuItemTypeInterface $menuItemType
     * @param array $rowData
     */
    public function extract(EntityInterface $menuItemType, ArrayAccess $rowData) {
        /** @var MenuItemTypeInterface $menuItemType */
        $rowData->offsetSet('type',  $menuItemType->getType()); // type je primární klíč - "readonly", hodnota pro where
    }
}
