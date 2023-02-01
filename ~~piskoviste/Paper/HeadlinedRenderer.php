<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Paper;

use Red\Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
use Red\Component\ViewModel\Content\Authored\Paper\NamedPaperViewModelInterface;
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
            $name = "named: ".$viewModel->getComponentAggregate()->getName();
        } else {
            $name = "presented";
        }

        if (isset($menuNode) AND isset($paper)) {
            $innerHtml = Html::tag('div', ['class'=>$this->classMap->get('Component', 'div div')],
                            Html::tag('headline', ['class'=>$this->classMap->get('Component', 'div div headline')], $paper->getPaper())
                        )
                        .Html::tag('content', ['class'=>$this->classMap->get('Component', 'div content')], $paper->getPaperSection());
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', [], 'No data item or article for rendering.');
            $style = "display: none;";
        }
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->get('Component', 'div'), 'style'=>$style], $innerHtml);
    }
}
