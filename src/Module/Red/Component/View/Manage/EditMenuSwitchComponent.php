<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Manage;

use Red\Component\View\ComponentAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of EditMenuSwitchComponent
 *
 * @author pes2704
 */
class EditMenuSwitchComponent extends ComponentAbstract {

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
