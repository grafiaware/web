<?php

namespace Events\Component\View\Data;

use Component\View\ComponentSingleItemAbstract;
use Component\View\ComponentItemInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/** 
 * 
 */ 
class DocumentSingleItemComponent extends ComponentSingleItemAbstract implements ComponentItemInterface {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => true], 
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
