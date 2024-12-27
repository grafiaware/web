<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Component\View\Data;

use Component\View\ComponentItemAbstract;
use Component\View\ComponentItemInterface;
use Component\View\ComponentPrototypeInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of CompanyComponentPrototype
 *
 * @author pes2704
 */
class TagComponent extends ComponentItemAbstract implements ComponentItemInterface, ComponentPrototypeInterface {
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
