<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Red\Service\ItemCreator\MenuItem;   // Static je keyword a použití Red\Service\ItemCreator\Static je syntaktická chyba

use Red\Service\ItemCreator\ItemCreatorInterface;
use Red\Service\ItemCreator\ItemCreatorAbstract;

use Red\Model\Entity\MenuItemInterface;

/**
 * Description of StaticService
 *
 * @author pes2704
 */
class MenuItemCreator extends ItemCreatorAbstract implements ItemCreatorInterface {

    /**
     * Nedělá nic. Je zde ale možnost do budoucna - tabulka menu s informacemi o položkách typu menu.
     * 
     * @param MenuItemInterface $menuItem
     * @return void
     */
    public function initialize(MenuItemInterface $menuItem): void {

    }

}
