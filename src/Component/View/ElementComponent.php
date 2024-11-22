<?php
namespace Component\View;

use Component\View\ComponentCompositeAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of ElementComponent
 *
 * @author pes2704
 */
class ElementComponent extends ComponentCompositeAbstract {
    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
