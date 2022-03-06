<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Pes\View\Template\PhpTemplate;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of ControlEditMenu
 *
 * @author pes2704
 */
class ToggleEditMenuComponent  extends StatusComponentAbstract {

    //renderuje template nebo NonPermittedContentRenderer

    public function beforeRenderingHook(): void {
        if($this->isAllowedToPresent(AccessPresentationEnum::DISPLAY)) {
            $this->setTemplate(new PhpTemplate($this->configuration->getTemplateControlEditMenu()));
        } else {
            $this->setRendererName(NoPermittedContentRenderer::class);
        }
    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => \Component\View\StatusComponentAbstract::class],
//            RoleEnum::EDITOR => [AllowedViewEnum::DISPLAY => \Component\View\StatusComponentAbstract::class],
        ];
    }
}
