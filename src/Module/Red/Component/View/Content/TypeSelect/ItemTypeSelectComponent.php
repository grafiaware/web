<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Content\TypeSelect;

use Component\View\ComponentCompositeAbstract;
use Red\Component\ViewModel\Content\TypeSelect\ItemTypeSelectViewModel;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of ItemTypeSelectComponent
 *
 * @author pes2704
 */
class ItemTypeSelectComponent extends ComponentCompositeAbstract {

    /**
     * @var ItemTypeSelectViewModel
     */
    protected $contextData;
    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => false, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => false, AccessPresentationEnum::EDIT => true],
        ];
    }
}
