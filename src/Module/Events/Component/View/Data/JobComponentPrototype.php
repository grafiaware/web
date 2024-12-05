<?php

namespace Events\Component\View\Data;

use Component\View\ComponentItemAbstract;
use Component\View\ComponentItemInterface;
use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

use Events\Component\View\Data\JobComponent;
use Component\View\ComponentPrototypeInterface;
/** 
 * 
 */ 
class JobComponentPrototype extends JobComponent implements ComponentPrototypeInterface {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
    
    public function __clone() {
        ;
    }
}
