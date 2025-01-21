<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Component\View\Data;

use Component\View\ComponentSingleListAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of RepresentativeActionComponent
 *
 * @author pes2704
 */
class DocumentSingleListComponent extends ComponentSingleListAbstract {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
        ];
    }

}
