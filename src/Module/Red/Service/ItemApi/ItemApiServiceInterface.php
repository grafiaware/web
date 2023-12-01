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
     * 
     * @param MenuItemInterface $menuItem
     */
    public function getLoaderApiUri(MenuItemInterface $menuItem);
            
            
}
