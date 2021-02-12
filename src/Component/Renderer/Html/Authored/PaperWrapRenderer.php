<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperWrapRenderer extends HtmlRendererAbstract {

    public function render($data=NULL) {
        return  Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div')], $data['article']);
    }
}
