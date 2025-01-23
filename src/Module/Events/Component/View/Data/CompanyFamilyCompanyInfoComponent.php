<?php

namespace Events\Component\View\Data;

use Component\View\ComponentItemAbstract;
use Component\View\ComponentItemInterface;
use Component\View\ComponentPrototypeInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/** 
 * 
 */ 
class CompanyFamilyCompanyInfoComponent extends ComponentItemAbstract implements ComponentItemInterface, ComponentPrototypeInterface {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
            ];
    } 
}
