<?php
namespace Component\Template;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Pes\View\Template\PhpTemplate;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;

/**
 * Description of PaperTemplate
 *
 * @author pes2704
 */
class PaperTemplate extends PhpTemplate {
    public function getDefaultRendererService(): string {
        return PaperRenderer::class;
    }
}