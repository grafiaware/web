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
        $paperAggregate = $viewModel->getPaper();
        if ($paperAggregate) {
            $html = Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/{$paperAggregate->getId()}/perex"],
                Html::tag('perex',
                    [
                        'id' => "perex_{$paperAggregate->getId()}",  // id musí být na stránce unikátní - skládám ze slova perex_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->get('Perex', 'perex.edit-html'),
                        'data-owner'=>$paperAggregate->getEditor()
                    ],
                    $paperAggregate->getPerex() ?? ""
                )
            );
        } else {
            $html = '';
        }
        return $html;
    }
}
