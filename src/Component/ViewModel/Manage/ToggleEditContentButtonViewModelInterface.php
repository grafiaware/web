<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Component\ViewModel\Manage;

use Red\Model\Entity\ItemActionInterface;
use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ToggleEditContentButtonViewModelInterface extends ViewModelInterface {
    public function setItemActionParams($typeFk, $itemId):void;
    public function getItemAction(): ItemActionInterface;
}
