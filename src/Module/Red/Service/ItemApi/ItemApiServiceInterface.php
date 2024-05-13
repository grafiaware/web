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
     * Generuje uri pro GET request požadující v API obsah celé stránky, která v části pro content obsahuje obsah odpovídající položce menu.
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getPageApiUri(MenuItemInterface $menuItem);
    
    /**
     * Generuje uri pro GET request požadující v API obsah driveru presentované (aktuální) položky menu.
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getPresentedDriverApiUri(MenuItemInterface $menuItem);

    /**
     * Generuje uri pro GET request požadující v API obsah driveru položky menu.
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getDriverApiUri(MenuItemInterface $menuItem);
    
    /**
     * Generuje uri pro GET request požadující v API obsah odpovídající položce menu.
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getContentApiUri(MenuItemInterface $menuItem);
            
    /**
     * Generuje uri pro GET request požadující v API nastavení titulku položky menu.
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getItemTitleApiUri(MenuItemInterface $menuItem);

}
