<?php

namespace Events\Component\View\Data;

use Component\View\ComponentFamilyListAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/** 
 * 
 */ 
class CompanyFamilyCompanyAddressListComponent extends ComponentFamilyListAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
