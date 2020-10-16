<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Model\Entity\PaperAggregateInterface;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;
use Component\ViewModel\Authored\Paper\PresentedPaperViewModelInterface;
use Pes\Text\Html;

use Component\View\Authored\ButtonsForm\PaperTemplateButtonsForm;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperEditableRenderer  extends AuthoredEditableRendererAbstract {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $viewModel->isArticleEditable();
        $paperAggregate = $viewModel->getPaperAggregate();

        // select render
        if (isset($paperAggregate)) {
            $innerHtml = $this->renderPaper($paperAggregate);
        } else {
            if ($viewModel instanceof PresentedPaperViewModelInterface) {
                $innerHtml = $this->renderSelectPaperTemplate($viewModel);
            } else {
                $innerHtml = $this->renderNoPaperContent($viewModel);
            }
        }

        // atribut data-component je jen pro info v html
        return Html::tag('div', ['data-componentinfo'=>$this->getComponentinfo($viewModel), 'class'=>$this->classMap->getClass('Segment', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div.paper')], $innerHtml)
            );
    }

    private function getComponentinfo(PaperViewModelInterface $viewModel) {
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $componentAggregate = $viewModel->getComponentAggregate();
            if (isset($componentAggregate)) {
                $componentinfo = "named: ".$componentAggregate->getName();
            } else {
                $componentinfo = "undefined component named: ".$viewModel->getComponentName();
            }
        } else {
            $componentinfo = "presented";
        }
        return $componentinfo;
    }

    private function renderPaper(PaperAggregateInterface $paperAggregate) {

            // TinyMCE v inline režimu pojmenovává proměnné v POSTu mce_XX, kde XX je asi pořadové číslo selected elementu na celé stránce, pokud editovaný element má id, pak TinyMCE použije toto id.
            // Použije ho tak, že přidá tag <input type=hidden name=id_editovaného_elementu> a této proměnné přiřadí hodnotu.
            // Samozřejmě id elementu musí být unikátní na stránce.

//                'onblur'=>'var throw=confirm("Chcete zahodit změny v obsahu headline?"); if (throw==false) {document.getElementByName("headline").focus(); }'

        return
                $this->renderPaperTemplateButtonsForm($paperAggregate).
        $this->renderPaperButtonsForm($paperAggregate).
                $this->renderHeadlineForm($paperAggregate).
                $this->renderPerexForm($paperAggregate).
                $this->renderContentsDivs($paperAggregate).
                    ""
                ;
    }

    private function renderSelectPaperTemplate(PresentedPaperViewModelInterface $viewModel) {
        $menuItemId = $viewModel->getPresentedMenuItem()->getId();
        return
            Html::tag('form', ['method' => 'POST', 'action' => 'api/v1/paper'],
                Html::tag('input', ['name'=>'menu_item_id', 'value'=>$menuItemId, 'type'=>'hidden'])
                .
                // 'paper_template_select' class je selektor v TinyInit.js
                Html::tag('div', ['id'=>'paper_template_html', 'class'=>'paper_template_select'],
                        Html::tag('p', [], '')
                    )
                );
    }

    private function renderNoPaperContent(PaperViewModelInterface $viewModel) {
        return Html::tag('div', [], "No paper for rendering. Component - '{$this->getComponentinfo($viewModel)}'.");
    }
}

