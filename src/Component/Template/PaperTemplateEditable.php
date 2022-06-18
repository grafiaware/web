<?php
namespace Component\Template;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Pes\View\Template\PhpTemplate;
use Component\Renderer\Html\Content\Authored\Paper\PaperRendererEditable;

/**
 * Description of PaperTemplate
 *
 * @author pes2704
 */
class PaperTemplateEditable extends PhpTemplate {
    public function getDefaultRendererService(): string {
        return PaperRendererEditable::class;
    }
}
