<?php
namespace Red\Service\Menu;

use Red\Component\View\Menu\DriverComponentInterface;

/**
 *
 * @author pes2704
 */
interface DriverServiceInterface {
    
    public function completeDriverComponent(DriverComponentInterface $driver, $uid);
}
