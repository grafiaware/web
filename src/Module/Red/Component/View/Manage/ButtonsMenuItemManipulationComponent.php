<?php
namespace Red\Component\View\Manage;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

/**
 * Description of ButtonsItemManipulationComponent
 *
 * @author pes2704
 */
class ButtonsMenuItemManipulationComponent extends ButtonsMenuComponentAbstract {


    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::EDIT => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::EDIT => static::class],
        ];
    }
}
