<?php

namespace Events\Component\View\Data;

use Component\View\ComponentFamilyItemAbstract;
use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/** 
 * 
 */ 
class CompanyFamilyJobComponent extends ComponentFamilyItemAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
