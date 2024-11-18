<?php
namespace Red\Component\View\Manage;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

/**
 * Description of ButtonsItemManipulationComponent
 *
 * @author pes2704
 */
class ButtonsMenuDeleteComponent extends ButtonsMenuComponentAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::EDIT => true],
        ];
    }
}
