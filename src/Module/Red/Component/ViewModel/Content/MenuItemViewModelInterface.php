<?php

namespace Red\Component\ViewModel\Content;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemViewModelInterface {
    public function getStatus(): StatusViewModelInterface;
    public function setMenuItemId($menuItemId);
    public function getMenuItemId();    
    public function getMenuItem(): MenuItemInterface;
    public function getComponentUid();

}
