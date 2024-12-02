<?php

namespace Events\Component\View\Data;

use Component\View\ComponentItemAbstract;
use Component\View\ComponentItemInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/** 
 * 
 */ 
class CompanyContactComponent extends ComponentItemAbstract implements ComponentItemInterface {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
            ];
    }
}
