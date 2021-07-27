<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Template;

use Pes\View\Template\ImplodeTemplate;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;

/**
 * Description of ImloldeParepTemplate
 *
 * @author pes2704
 */
class ImplodePaperTemplateEditable extends ImplodeTemplate {
    public function getDefaultRendererService(): string {
        return PaperRendererEditable::class;
    }

}
