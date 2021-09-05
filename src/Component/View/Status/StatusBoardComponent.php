<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Status;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\ViewModel\Status\StatusBoardViewModelInterface;
use Pes\View\Template\PhpTemplate;

use Component\View\RoleEnum;
use Component\View\AllowedActionEnum;

/**
 * Description of StatusBoadComponent
 *
 * @author pes2704
 */
class StatusBoardComponent extends StatusComponentAbstract {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    //renderuje template nebo NonPermittedContentRenderer

    public function beforeRenderingHook(): void {
        if($this->isAllowed($this, AllowedActionEnum::DISPLAY)) {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateStatusBoard()));
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AllowedActionEnum::DISPLAY => static::class],
        ];
    }
}
