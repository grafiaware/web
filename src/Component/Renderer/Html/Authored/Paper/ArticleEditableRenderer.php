<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Pes\Text\Html;
use Pes\View\Renderer\ImplodeRenderer;

use Component\View\Authored\Paper\ButtonsForm\PaperTemplateButtonsForm;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleEditableRenderer  extends ArticleRendererAbstract {
    public function render($data=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $viewModel = $this->viewModel;
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
        if (!isset($paperAggregate)) {
            $paperAggregate = $viewModel->getNewPaper();  // vrací Paper
        }
        if (isset($paperAggregate)) {
            $articleButtonForms = $this->renderPaperTemplateButtonsForm($paperAggregate) . $this->renderPaperButtonsForm($paperAggregate);

            $sectionHeadline = $this->renderHeadlineEditable($paperAggregate);
            $sectionPerex = $this->renderPerexEditable($paperAggregate);
            $content = ($paperAggregate instanceof PaperAggregatePaperContentInterface) ? $this->renderContentsEditable($paperAggregate) : "";

            $html = Html::tag('article', ['data-red-renderer'=>'PaperEditable', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                        $articleButtonForms
                        .Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/{$paperAggregate->getId()}"],
                            $sectionHeadline.$sectionPerex.$content
                        )
                    );
        } else {
            $html = Html::tag('div', ['style' => "display: none;"], "No paper for remderimg.");
        }
        return $html;
    }
}