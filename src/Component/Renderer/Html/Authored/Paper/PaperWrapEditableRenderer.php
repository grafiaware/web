<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;

use Pes\Text\Html;
use Pes\View\Renderer\ImplodeRenderer;

use Component\View\Authored\Paper\ButtonsForm\PaperTemplateButtonsForm;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperWrapEditableRenderer  extends HtmlRendererAbstract {
    public function render($data=NULL) {
        // atribut data-componentinfo je jen pro info v html
        return Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div.paper')], $data['article'])
            );
    }
}

