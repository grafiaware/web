<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Red\Component\View\Menu;

use Red\Component\View\Menu\MenuComponentAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of MenuComponentRed
 *
 * @author pes2704
 */
class MenuComponentEventsVisitor extends MenuComponentAbstract {
    public static function getComponentPermissions(): array {
        return [
//            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
//            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::VISITOR => [AccessPresentationEnum::DISPLAY => true]
        ];
    }
    
}
