<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;
use Pes\Text\Html;

/**
 * Description of HeadlinedRenderer
 *
 * @author pes2704
 */
class HeadlinedRenderer extends HtmlRendererAbstract {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $menuNode = $viewModel->getHierarchyNode();
        $paper = $viewModel->getMenuItemPaperAggregate();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponent()->getName();
        } else {
            $name = "presented";
        }

        if (isset($menuNode) AND isset($paper)) {
            $innerHtml = Html::tag('div', ['class'=>$this->classMap->getClass('Component', 'div div')],
                            Html::tag('headline', ['class'=>$this->classMap->getClass('Component', 'div div headline')], $paper->getPaperHeadline())
                        )
                        .Html::tag('content', ['class'=>$this->classMap->getClass('Component', 'div content')], $paper->getPaperContent());
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', [], 'No data item or article for rendering.');
            $style = "display: none;";
        }
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Component', 'div'), 'style'=>$style], $innerHtml);
    }
}
