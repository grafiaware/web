<?php
namespace Red\Service\ItemApi;

use Pes\View\ViewInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface ItemApiServiceInterface {
    
    /**
     * Generuje uri pro GET request požadující v API obsah celé stránky, která v části pro content obsahuje obsah položky menu.
     * Používá položky MenuItemInterface a z hodnoty uidFk vytvoří uri pro volání API.
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getPageApiUri(MenuItemInterface $menuItem);
    
    /**
     * Generuje uri pro GET request požadující v API obsah odpovídající položce menu.
     * Používá položky MenuItemInterface a z hodnot apiModule, apiGenerator a id vytvoří uri pro volání API.
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getContentApiUri(MenuItemInterface $menuItem);
            
            
}
