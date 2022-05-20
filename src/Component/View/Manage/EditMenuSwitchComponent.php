<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of EditMenuSwitchComponent
 *
 * @author pes2704
 */
class EditMenuSwitchComponent  extends StatusComponentAbstract {

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
