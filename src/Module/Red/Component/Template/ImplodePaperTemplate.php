<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Template;

use Pes\View\Template\ImplodeTemplate;
use Red\Component\Renderer\Html\Content\Authored\Paper\PaperRenderer;

/**
 * Description of ImloldeParepTemplate
 *
 * @author pes2704
 */
class ImplodePaperTemplate extends ImplodeTemplate {
    public function getDefaultRendererService(): string {
        return PaperRenderer::class;
    }

}
