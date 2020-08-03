<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;

use Pes\Text\Html;

/**
 * Description of HeadlinedEditableRenderer
 *
 * @author pes2704
 */
class HeadlinedEditableRenderer extends PaperEditableRendererAbstract {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $paperAggregate = $viewModel->getPaperAggregate();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponentAggregate()->getName();
        } else {
            $name = "presented";
        }

        if (isset($paperAggregate)) {
            $innerHtml = $this->renderPaper($paperAggregate);
        } else {
            $innerHtml = Html::tag('div', [], 'Missing paper for rendering.');
        }
        // atribut data-component je jen pro info v html
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Segment', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div.grafia')], $innerHtml)
            );
    }

}