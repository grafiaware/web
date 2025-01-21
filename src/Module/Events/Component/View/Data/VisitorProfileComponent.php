<?php

namespace Events\Component\View\Data;

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
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::VISITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true]
        ];
    }
}
