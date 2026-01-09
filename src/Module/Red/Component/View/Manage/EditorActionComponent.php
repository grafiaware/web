<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Manage;

use Component\View\ComponentCompositeAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of UserActionComponent
 *
 * @author pes2704
 */
class EditorActionComponent extends ComponentCompositeAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EVENTS_ADMINISTRATOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            
        ];
    }
}
