<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Manage;

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

    //renderuje template nebo NonPermittedContentRenderer

//    public function beforeRenderingHook(): void {
//        if($this->isAllowedToPresent(AccessPresentationEnum::DISPLAY)) {
//            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateStatusBoard()));
//        } else {
//            $this->setRendererName(NoPermittedContentRenderer::class);
//        }
//    }

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::VISITOR => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::REPRESENTATIVE => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
