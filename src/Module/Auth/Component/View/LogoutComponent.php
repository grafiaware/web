<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Component\View;

use Component\View\ComponentCompositeAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of LogoutComponent
 *
 * @author pes2704
 */
class LogoutComponent extends ComponentCompositeAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    public static function getComponentPermissions(): array {
        // komponent vidí jen příhlášení
        return [
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
