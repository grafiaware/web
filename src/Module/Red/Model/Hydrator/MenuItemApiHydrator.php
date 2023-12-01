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

use Red\Model\Entity\MenuItemApiInterface;

/**
 * Description of MenuItemTypeHydrator
 *
 * @author pes2704
 */
class MenuItemApiHydrator implements HydratorInterface {
    
    /**
     * 
     * @param EntityInterface $menuItemApi
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $menuItemApi, ArrayAccess $rowData) {
        /** @var MenuItemApiInterface $menuItemApi */
        $menuItemApi->setModule($rowData->offsetGet('module'));
        $menuItemApi->setGenerator($rowData->offsetGet('generator'));
    }

    /**
     * 
     * @param EntityInterface $menuItemApi
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $menuItemApi, ArrayAccess $rowData) {
        /** @var MenuItemApiInterface $menuItemApi */
        $rowData->offsetSet('module',  $menuItemApi->getModule()); //  "readonly", hodnota pro where
        $rowData->offsetSet('generator',  $menuItemApi->getGenerator()); //  "readonly", hodnota pro where
    }
}
