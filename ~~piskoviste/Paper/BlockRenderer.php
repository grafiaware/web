<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Content\Authored\Paper\NamedPaperViewModelInterface;

use Pes\Text\Html;

/**
 * Description of BlockRenderer
 *
 * @author pes2704
 */
class BlockRenderer extends HtmlRendererAbstract {

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
            $innerHtml = $paper->getPaperSection();
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', ['data-component'=>$name], 'No data item or article for rendering.');
            $style = "display: none;";
        }
        return Html::tag('block', ['data-component'=>$name, 'class'=>$this->classMap->get('Component', 'block'), 'style'=>$style], $innerHtml);
    }
}
