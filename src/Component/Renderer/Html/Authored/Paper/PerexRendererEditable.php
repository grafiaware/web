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
 * Description of PerexEditableRenderer
 *
 * @author pes2704
 */
class PerexRendererEditable extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paper = $viewModel->getPaper();
        return
            Html::tag('section', ['class'=>$this->classMap->getClass('Perex', 'section')],
                Html::tag('perex',
                    [
                        'id' => "perex_{$paper->getId()}",  // id musí být na stránce unikátní - skládám ze slova perex_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->getClass('Perex', 'perex'),
                        'data-owner'=>$paper->getEditor()
                    ],
                    Html::tag('div', ['class'=>"edit-html"], $paper->getPerex() ?? "")
                    )
            );
    }
}
