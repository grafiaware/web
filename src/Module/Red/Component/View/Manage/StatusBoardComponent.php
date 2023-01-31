<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Manage;

use Red\Component\View\ComponentCompositeAbstract;
use Red\Component\Renderer\Html\NoPermittedContentRenderer;
use Red\Component\ViewModel\Manage\StatusBoardViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of StatusBoadComponent
 *
 * @author pes2704
 */
class StatusBoardComponent extends ComponentCompositeAbstract {

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

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
