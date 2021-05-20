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
        $paper = $viewModel->getPaper();  // vrací PaperAggregate
        if (!isset($paper)) {
            $paper = $viewModel->getNewPaper();  // vrací Paper
        }
        if (isset($paper)) {
            $articleButtons = $this->renderPaperTemplateButtonsForm($paper) . $this->renderPaperButtonsForm($paper);
            $section = $this->renderHeadlineForm($paper) . $this->renderPerexForm($paper);
            $content = ($paper instanceof PaperAggregateInterface) ? $this->renderContentsDivs($paper) : "";

            $html = Html::tag('article', ['data-red-renderer'=>'PaperEditable', "data-red-datasource"=> "paperByReference{$paper->getMenuItemIdFk()}"],
                    $articleButtons
                    .Html::tag('section', [], $section)
                    .Html::tag('content', [], $content)
                    );
        } else {
            $html = Html::tag('div', ['style' => "display: none;"], "No paper for remderimg.");
        }
        return $html;
    }
}