<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;
use Pes\Text\Html;

/**
 * Description of HeadlinedRenderer
 *
 * @author pes2704
 */
class HeadlinedRenderer extends AuthoredRendererAbstract {
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
            $innerHtml = $this->renderHeadline($paperAggregate)
                        .$this->renderPerex($paperAggregate)
                        .$this->renderContents($paperAggregate);
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', [], 'No paper for rendering.');
            $style = "display: none;";
        }
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Segment', 'div'), 'style'=>$style], $innerHtml);
    }
}
