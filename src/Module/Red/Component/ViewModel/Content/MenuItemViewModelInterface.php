<?php

namespace Red\Component\ViewModel\Content;

use Component\ViewModel\ViewModelInterface;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemViewModelInterface extends ViewModelInterface {
    
    /**
     * Vrací StatusViewModel
     * @return StatusViewModelInterface
     */
    public function getStatusViewModel(): StatusViewModelInterface;
    
    /**
     * Nastaví id objektu MenuItemInterface, který bude vracet metoda getMenuItem().
     * Hodnota MenuItem id je nutná pro načtení ItemAction.
     * 
     * @param type $menuItemId
     */
    public function setMenuItemId($menuItemId);
    
    /**
     * Vrací id objektu MenuItemInterface nastavené metodou setMenuItemId($menuItemId).
     * Hodnota MenuItem id je nutná pro načtení ItemAction.
     */
    public function getMenuItemId();
    
    /**
     * Vrací entitu MenuItemInterface s id zadaným metodou setMenuItemId($menuItemId).
     * @return MenuItemInterface
     */
    public function getMenuItem(): ?MenuItemInterface;
    
    /**
     * Vrací náhodně generovaný identifikátor objektu komponenty. 
     */
    public function getComponentUid();

}
