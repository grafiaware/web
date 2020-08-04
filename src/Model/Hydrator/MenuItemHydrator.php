<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\MenuItemInterface;
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
     * @param type $row
     */
    public function hydrate(EntityInterface $menuItem, &$row) {
        /** @var MenuItemInterface $menuItem */
        $menuItem
            ->setLangCodeFk($row['lang_code_fk'])
            ->setUidFk($row['uid_fk'])
            ->setType($row['type_fk'])
            ->setId($row['id'])
            ->setTitle($row['title'])
            ->setActive((bool) $row['active'])
            ;
    }

    /**
     *
     * @param MenuItemInterface $menuItem
     * @param type $row
     */
    public function extract(EntityInterface $menuItem, &$row=[]) {
        /** @var MenuItemInterface $menuItem */
        $row['lang_code_fk'] = $menuItem->getLangCodeFk();
        $row['uid_fk'] = $menuItem->getUidFk();
        $row['type_fk'] = $menuItem->getTypeFk();
        $row['id'] = $menuItem->getId();
        $row['title'] = $menuItem->getTitle();
        $row['active'] = (int) $menuItem->getActive();
    }
}
