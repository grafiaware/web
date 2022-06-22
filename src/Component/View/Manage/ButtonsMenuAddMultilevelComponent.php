<?php
namespace Component\View\Manage;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

/**
 * Description of ButtonsMenuManipulationComponent
 *
 * @author pes2704
 */
class ButtonsMenuAddMultilevelComponent extends ButtonsMenuComponentAbstract {

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::EDIT => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::EDIT => static::class],
        ];
    }
}
