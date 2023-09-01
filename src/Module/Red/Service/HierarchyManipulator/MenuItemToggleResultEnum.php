<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Red\Service\HierarchyManipulator;

use Pes\Type\Enum;

/**
 * Description of MenuItemToggleResultEnum
 *
 * @author pes2704
 */
class MenuItemToggleResultEnum extends Enum {
    const DEACTOVATE_ONE = "deactivated one item";
    const DEACTOVATE_WITH_DESCENDANTS = "deactivated with descendants";
    const ACTIVATE_ONE = "activated one item";
    const UNABLE_ACTIVATE = "unable activate item";
}
