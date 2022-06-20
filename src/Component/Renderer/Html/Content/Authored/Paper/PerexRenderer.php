<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Content\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;

use Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;

use Pes\Text\Html;

/**
 * Description of PerexRenderer
 *
 * @author pes2704
 */
class PerexRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paper = $viewModel->getPaper();
        if (isset($paper)) {
            $html = Html::tag('perex',
                        ['class'=>$this->classMap->get('Perex', 'perex')],
                        $paper->getPerex() ?? ""
                );
        } else {
            $html = '';
        }
        return $html;
    }
}
