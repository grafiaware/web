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

use Component\Renderer\Html\Authored\Paper\Buttons;

use Component\View\Authored\Paper\ButtonsForm\PaperTemplateButtonsForm;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperWrapEditableRenderer  extends HtmlRendererAbstract {
    public function render($data=NULL) {
        $selectTemplate = isset($buttons) ? $buttons->renderPaperTemplateButtonsForm($paperAggregate) : "";
        $paperButton = isset($buttons) ? $buttons->getPaperButtonsForm($paperAggregate) : "";
        $article = $data['article'];
        // atribut data-componentinfo je jen pro info v html
        return Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div.paper')], $selectTemplate.$paperButton.$article

                )
            );
    }
}

