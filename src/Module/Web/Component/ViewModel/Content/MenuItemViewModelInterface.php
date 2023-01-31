<?php

namespace Web\Component\ViewModel\Content;

use Web\Component\ViewModel\StatusViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemViewModelInterface {
    public function getStatus(): StatusViewModelInterface;
    public function setMenuItemId($menuItemId);
    public function getMenuItem(): MenuItemInterface;
    public function getComponentUid();

}
