<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Pes\Text\Html;
use Pes\View\Renderer\ImplodeRenderer;

use Component\View\Authored\Paper\ButtonsForm\PaperTemplateButtonsForm;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRenderer  extends PaperRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
        if ($viewModel->isArticleEditable()) {
            $articleButtonForms = $this->renderPaperButtonsForm($paperAggregate);

            $headline = $this->renderHeadlineEditable($paperAggregate);
            $perex = $this->renderPerexEditable($paperAggregate);
            $contents = ($paperAggregate instanceof PaperAggregatePaperContentInterface) ? $this->renderContentsEditable($paperAggregate) : "";

            $html = Html::tag('article', ['data-red-renderer'=>'ArticleEditableRenderer', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                        $articleButtonForms
                        .Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/{$paperAggregate->getId()}"],
                            $headline.$perex.$contents
                        )
                    );
        } else {
            $paperAggregate = $viewModel->getPaper();  // vrací Paper nebo PaperAggregate
            $headline = Html::tag('div',
                        ['class'=>$this->classMap->getClass('Headline', 'div'),
                         'style' => "display: block;"
                        ],
                        $this->renderHeadline($paperAggregate)
                    );
            $perex = $this->renderPerex($paperAggregate);
            $contents = ($paperAggregate instanceof PaperAggregatePaperContentInterface) ? $this->renderContents($paperAggregate) : "";
            $html =
                Html::tag('article', ['data-red-renderer'=>'PaperEditable'],
                        $headline.$perex.$contents
                );
        }
        return $html;
    }
}