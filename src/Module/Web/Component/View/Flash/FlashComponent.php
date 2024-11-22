<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\View\Flash;

use Component\View\ComponentCompositeAbstract;
use Web\Component\ViewModel\Flash\FlashViewModelInterface;

/**
 * Description of FlashComponent
 *
 * @author pes2704
 */
class FlashComponent extends ComponentCompositeAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
