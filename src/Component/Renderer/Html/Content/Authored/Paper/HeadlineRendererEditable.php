<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Content\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;

use Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
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
                        'id'=>PaperControler::HEADLINE_CONTENT."{$paperId}_{$componentUid}",  // id musí být na stránce unikátní - skládám ze slova headline_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
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
