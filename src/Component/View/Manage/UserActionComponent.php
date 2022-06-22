<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Manage;

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
class UserActionComponent extends ComponentCompositeAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    //renderuje template nebo NonPermittedContentRenderer

//    public function beforeRenderingHook(): void {
//        if($this->isAllowedToPresent(AccessPresentationEnum::DISPLAY)) {
//            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateUserAction()));
//        } else {
//            $this->setRendererName(NoPermittedContentRenderer::class);
//        }
//    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
