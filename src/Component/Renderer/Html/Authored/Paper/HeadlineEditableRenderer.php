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
 * Description of HeadlineEditableRenderer
 *
 * @author pes2704
 */
class HeadlineEditableRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paper = $viewModel->getPaper();
        return
            Html::tag('section', ['class'=>$this->classMapEditable->getClass('Headline', 'section')],
                Html::tag(
                    'headline',
                    [
                        'id'=>"headline_{$paper->getId()}",  // id musí být na stránce unikátní - skládám ze slova headline_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMapEditable->getClass('Headline', 'headline'),
                    ],
                    Html::tag('div', ['class'=>"edit-text"], $paper->getHeadline() ?? "")
                )
            );
    }
}
