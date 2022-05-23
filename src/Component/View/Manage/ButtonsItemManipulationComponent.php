<?php
namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

/**
 * Description of ButtonsItemManipulationComponent
 *
 * @author pes2704
 */
class ButtonsItemManipulationComponent extends StatusComponentAbstract {
    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
