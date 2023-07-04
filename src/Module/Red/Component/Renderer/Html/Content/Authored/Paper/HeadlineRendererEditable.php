<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Content\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;

use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Middleware\Redactor\Controler\PaperControler;

use Pes\Text\Html;

/**
 * Description of HeadlineRenderer
 *
 * @author pes2704
 */
class HeadlineRendererEditable extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();
        if (isset($paperAggregate)) {
            /** @var PaperAggregatePaperSectionInterface $paperAggregate */
            $paperId = $paperAggregate->getId();
            $componentUid = $viewModel->getComponentUid();
            $html = Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/headline"],
            Html::tag('headline',
                    [
                        'id'=> implode("_", [PaperControler::HEADLINE_CONTENT, $paperId, $componentUid]),           // POZOR - id musí být unikátní - jinak selhává tiny selektor
                        'data-red-menuitemid'=>$viewModel->getMenuItemId(),
                        'class'=>$this->classMap->get('Headline', 'headline.edit-text'),
                    ],
                    $paperAggregate->getHeadline() ?? ""
                )
            );
        } else {
            $html = '';
        }
        return $html;
    }
}
