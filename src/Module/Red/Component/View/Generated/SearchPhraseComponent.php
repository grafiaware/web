<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Generated;

use Component\View\ComponentCompositeAbstract;
use Red\Component\ViewModel\Generated\SearchPhraseViewModel;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of SearchPhraseComponent
 *
 * @author pes2704
 */
class SearchPhraseComponent extends ComponentCompositeAbstract {
    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
