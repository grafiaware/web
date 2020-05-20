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

use Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of BlockRenderer
 *
 * @author pes2704
 */
class BlockRenderer extends AuthoredRendererAbstract {

    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $menuNode = $viewModel->getMenuNode();
        $paper = $viewModel->getPaper();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponent()->getName();
        } else {
            $name = "presented";
        }

        if (isset($menuNode) AND isset($paper)) {
            $innerHtml = $this->renderContents($paper);
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', [], 'No data item or article for rendering.');
            $style = "display: none;";
        }
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Segment', 'div'), 'style'=>$style], $innerHtml);
    }
}
