<?php

namespace Events\Component\View\Data;

//use Component\View\ComponentCompositeAbstract;
use Component\View\ComponentItemAbstract;
use Component\View\ComponentItemInterface;


use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/** 
 * 
 */ 
class VisitorProfileComponent extends  ComponentItemAbstract implements ComponentItemInterface {

    public static function getComponentPermissions(): array {
        return [
//            RoleEnum::VISITOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
//            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
//            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => static::class],
            
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::VISITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
