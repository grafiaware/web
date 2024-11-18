<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Content\Authored;

use Component\View\ComponentCompositeAbstract;
use Configuration\ComponentConfigurationInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of AuthoredComponentAbstract
 * Objekt je potomkem CompositeView.
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends ComponentCompositeAbstract implements AuthoredComponentInterface {

    const BUTTON_EDIT_CONTENT = 'buttonEditContent';
    const SELECT_TEMPLATE = 'selectTemplate';

    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
