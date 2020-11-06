<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\MenuItemTypeInterface;

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
    public function hydrate(EntityInterface $menuItemType, &$row) {
        /** @var MenuItemTypeInterface $menuItemType */
        $menuItemType->setType($row['type']);
    }

    /**
     *
     * @param MenuItemTypeInterface $menuItemType
     * @param array $row
     */
    public function extract(EntityInterface $menuItemType, &$row) {
        // type je primární klíč - "readonly"
    }
}
