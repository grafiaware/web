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

use Red\Model\Entity\BlockAggregateMenuItemInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 * Description of ComponentChildHydrator
 *
 * @author pes2704
 */
class BlockChildHydrator implements HydratorInterface {

    /**
     * Nastaví do agregátu MenuItem, pokud existuje. MenuItem je závislý na kontextu a tedy může být null (neaktivní nebo neaktuální menu item)
     *
     * @param BlockAggregateMenuItemInterface $componentAggregate
     * @param type $rowData
     */
    public function hydrate(EntityInterface $componentAggregate, ArrayAccess $rowData) {
        /** @var BlockAggregateMenuItemInterface $componentAggregate */
        $componentAggregate->setMenuItem($rowData->offsetGet(MenuItemInterface::class));
    }

    /**
     *
     * @param BlockAggregateMenuItemInterface $componentAggregate
     * @param array $rowData
     */
    public function extract(EntityInterface $componentAggregate, ArrayAccess $rowData) {
        /** @var BlockAggregateMenuItemInterface $componentAggregate */
        $rowData->offsetSet(MenuItemInterface::class, $componentAggregate->getMenuItem());
    }

}
