<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Status;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\ViewModel\StatusViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Component\View\RoleEnum;
use Component\View\AllowedActionEnum;

/**
 * Description of ControlEditMenu
 *
 * @author pes2704
 */
class ButtonEditMenuComponent  extends StatusComponentAbstract {

    //renderuje template nebo NonPermittedContentRenderer

    public function beforeRenderingHook(): void {
        if($this->isAllowed($this, AllowedActionEnum::EDIT)) {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateControlEditMenu()));
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }
}
