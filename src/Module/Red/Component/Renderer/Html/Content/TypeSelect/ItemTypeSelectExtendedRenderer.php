<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Red\Component\Renderer\Html\Content\TypeSelect;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Middleware\Redactor\Controler\ItemEditControler;
use Red\Component\ViewModel\Content\TypeSelect\ItemTypeSelectViewModelInterface;
use Pes\Text\Message;
use Pes\Text\Html;

use View\Includer;

/**
 * Description of ItemTypeSelectExtendedRenderer
 *
 * @author pes2704
 */
class ItemTypeSelectExtendedRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel = NULL) {
        $includer = new Includer();
        return $includer->protectedIncludeScope(__DIR__ . '/ext.php');
    }
}
