<?php

namespace Events\Component\View\Data;

use Component\View\ComponentCompositeAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/** 
 * 
 */ 
class RepresentativeCompanyAddressComponent extends ComponentCompositeAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::EDIT => static::class],
        ];
    }
}
