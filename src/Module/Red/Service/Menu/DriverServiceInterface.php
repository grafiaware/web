<?php
namespace Red\Service\Menu;

use Red\Model\Entity\MenuItemInterface;
use Red\Component\View\Menu\DriverComponentInterface;

/**
 *
 * @author pes2704
 */
interface DriverServiceInterface {
    
    /**
     * 
     * @param type $uid
     * @return MenuItemInterface|null
     */
    public function getMenuItem($uid): ?MenuItemInterface;
    
    /**
     * Doplní drive komponent o buttony
     * 
     * @param DriverComponentInterface $driver
     * @param MenuItemInterface $menuItem
     * @param bool $isPresented
     * @return DriverComponentInterface
     */
    public function completeDriverComponent(DriverComponentInterface $driver, MenuItemInterface $menuItem, bool $isPresented): DriverComponentInterface;
}
