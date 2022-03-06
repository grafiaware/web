<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Component\ViewModel;

use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemViewModelInterface {

    public function setMenuItemId($menuItemId);
    public function getMenuItem(): MenuItemInterface;
}
