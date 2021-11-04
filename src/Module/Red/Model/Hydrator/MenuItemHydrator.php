<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Red\Model\Entity\MenuItemInterface;

use Pes\Type\Date;

/**
 * Description of MenuItemHydrator
 *
 * @author pes2704
 */
class MenuItemHydrator implements HydratorInterface {

    /**
     *
     * @param MenuItemInterface $menuItem
     * @param type $rowData
     */
    public function hydrate(EntityInterface $menuItem, RowDataInterface $rowData) {
        /** @var MenuItemInterface $menuItem */
        $menuItem
            ->setLangCodeFk($rowData->offsetGet('lang_code_fk'))
            ->setUidFk($rowData->offsetGet('uid_fk'))
            ->setType($rowData->offsetGet('type_fk'))
            ->setId($rowData->offsetGet('id'))
            ->setTitle($rowData->offsetGet('title'))
            ->setPrettyuri($rowData->offsetGet('prettyuri'))
            ->setActive((bool) $rowData->offsetGet('active'))
            ;
    }

    /**
     *
     * @param MenuItemInterface $menuItem
     * @param type $rowData
     */
    public function extract(EntityInterface $menuItem, RowDataInterface $rowData) {
        /** @var MenuItemInterface $menuItem */
        $rowData->offsetSet('lang_code_fk', $menuItem->getLangCodeFk());
        $rowData->offsetSet('uid_fk', $menuItem->getUidFk());
        $rowData->offsetSet('type_fk', $menuItem->getTypeFk());
        $rowData->offsetSet('id', $menuItem->getId());
        $rowData->offsetSet('title', $menuItem->getTitle());
        $rowData->offsetSet('prettyuri', $menuItem->getPrettyuri());
        $rowData->offsetSet('active', (int) $menuItem->getActive());
    }
}
