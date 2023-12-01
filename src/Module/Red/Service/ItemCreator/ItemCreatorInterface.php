<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Service\ItemCreator;

use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface ItemCreatorInterface {
    
    /**
     * Založí potřebné datové struktury pro generování obsahu, např. záznamy v databázi a soubory template
     * @param MenuItemInterface $menuItem
     * @return void
     */
    public function initialize(MenuItemInterface $menuItem): void;
}
