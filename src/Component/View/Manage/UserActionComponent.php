<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Component\View\RoleEnum;
use Component\View\AllowedActionEnum;

/**
 * Description of UserActionComponent
 *
 * @author pes2704
 */
class UserActionComponent extends StatusComponentAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    //renderuje template nebo NonPermittedContentRenderer

    public function beforeRenderingHook(): void {
        if($this->isAllowed($this, AllowedActionEnum::EDIT)) {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateUserAction()));
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }
}
