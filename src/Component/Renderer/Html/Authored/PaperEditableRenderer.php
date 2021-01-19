<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Model\Entity\PaperAggregateInterface;
use Model\Entity\PaperInterface;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Pes\Text\Html;

use Component\View\Authored\Paper\ButtonsForm\PaperTemplateButtonsForm;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperEditableRenderer  extends AuthoredRendererAbstract {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $paperAggregate = $viewModel->getPaperAggregate();

        $innerHtml = $this->renderPaper($paperAggregate);
        if ($paper instanceof PaperAggregateInterface) {
            $paper->
                    $contents = $paperAggregate->getPaperContentsArraySorted(PaperAggregateInterface::BY_PRIORITY);
        }
        // atribut data-component je jen pro info v html
        return Html::tag('div', ['data-componentinfo'=>$viewModel->getInfo(), 'class'=>$this->classMap->getClass('Segment', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div.paper')], $innerHtml)
            );
    }


    private function renderPaper(PaperInterface $paper) {

            // TinyMCE v inline režimu pojmenovává proměnné v POSTu mce_XX, kde XX je asi pořadové číslo selected elementu na celé stránce, pokud editovaný element má id, pak TinyMCE použije toto id.
            // Použije ho tak, že přidá tag <input type=hidden name=id_editovaného_elementu> a této proměnné přiřadí hodnotu.
            // Samozřejmě id elementu musí být unikátní na stránce.

//                'onblur'=>'var throw=confirm("Chcete zahodit změny v obsahu headline?"); if (throw==false) {document.getElementByName("headline").focus(); }'

        return
                $this->renderPaperTemplateButtonsForm($paper).
        $this->renderPaperButtonsForm($paper).
                $this->renderHeadlineForm($paper).
                $this->renderPerexForm($paper).
                    ""
                ;
    }
}