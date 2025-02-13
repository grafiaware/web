<?php
namespace Red\Service\Menu;

use Red\Model\Entity\MenuItemInterface;
use Red\Component\View\Menu\DriverComponentInterface;

/**
 *
 * @author pes2704
 */
interface DriverServiceInterface {
    
    public function getMenuItem($uid);
    
    public function completeDriverComponent(DriverComponentInterface $driver, MenuItemInterface $menuItem, $isPresented, $itemType);
}
