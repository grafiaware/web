<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Component\View\Data;

use Component\View\ComponentCollectionAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of RepresentativeActionComponent
 *
 * @author pes2704
 */
class CompanyComponent extends ComponentCollectionAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
