<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\View\Info;

use Component\View\ComponentCompositeAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Red\Component\ViewModel\Manage\InfoBoardViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of StatusBoadComponent
 *
 * @author pes2704
 */
class InfoBoardComponent extends ComponentCompositeAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::VISITOR => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => true],
        ];
    }

}
