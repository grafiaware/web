<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Content\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
use Red\Middleware\Redactor\Controler\PaperControler;

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
            $paperId = $paperAggregate->getId();
            $componentUid = $viewModel->getComponentUid();
            $html = Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/perex"],
                Html::tag('perex',
                    [
                        'id'=> implode("_", [PaperControler::PEREX_CONTENT, $paperId, $componentUid]),           // POZOR - id musí být unikátní - jinak selhává tiny selektor
                        'data-red-menuitemid'=>$viewModel->getMenuItemId(),
                        'class'=>$this->classMap->get('Perex', 'perex.edit-html')
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
