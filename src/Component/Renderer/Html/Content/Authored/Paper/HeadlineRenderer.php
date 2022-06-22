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
 * Description of HeadlineRenderer
 *
 * @author pes2704
 */
class HeadlineRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paper = $viewModel->getPaper();
        if (isset($paper)) {
            $html = Html::tag('headline',
                            ['class'=>$this->classMap->get('Headline', 'headline')],
                            $paper->getHeadline() ?? ""
                    );
        } else {
            $html = '';
        }
        return $html;
    }
}
