<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperWrapRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        if ($viewModel->isArticleEditable()) {
            $selectTemplate = isset($buttons) ? $buttons->renderPaperTemplateButtonsForm($paperAggregate) : "";
            $paperButton = isset($buttons) ? $buttons->renderPaperButtonsForm($paperAggregate) : "";
            $article = $data['article'];
            // atribut data-componentinfo je jen pro info v html
            return Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div.paper')], $selectTemplate.$paperButton.$article

                )
            );
        } else {
            return  Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div')], $data['article']);
        }
    }
}
