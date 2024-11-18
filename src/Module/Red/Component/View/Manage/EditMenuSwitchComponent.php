<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Manage;

use Component\View\ComponentCompositeAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of EditMenuSwitchComponent
 *
 * @author pes2704
 */
class EditMenuSwitchComponent extends ComponentCompositeAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
        ];
    }
}
