<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;

use Pes\Text\Html;

/**
 * Description of HeadlinedEditableRenderer
 *
 * @author pes2704
 */
class HeadlinedEditableRenderer extends AuthoredEditableRendererAbstract {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $paperAggregate = $viewModel->getPaperAggregate();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponentAggregate()->getName();
        } else {
            $name = "presented";
        }

        if (isset($paperAggregate)) {

            // TinyMCE v inline režimu pojmenovává proměnné v POSTu mce_XX, kde XX je asi pořadové číslo selected elementu na celé stránce, pokud editovaný element má id, pak TinyMCE použije toto id.
            // Použije ho tak, že přidá tag <input type=hidden name=id_editovaného_elementu> a této proměnné přiřadí hodnotu.
            // Samozřejmě id elementu musí být unikátní na stránce.

//                'onblur'=>'var throw=confirm("Chcete zahodit změny v obsahu headline?"); if (throw==false) {document.getElementByName("headline").focus(); }'

            $innerHtml =
                 $this->renderPaperButtonsForm($paperAggregate)
                .$this->renderHeadlineForm($paperAggregate)
                .$this->renderPerexForm($paperAggregate)
                .$this->renderContentsDivs($paperAggregate)
                ;
        } else {
            $innerHtml = Html::tag('div', [], 'Missing paper for rendering.');
        }
        // atribut data-component je jen pro info v html
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Segment', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Segment', 'div div')], $innerHtml)
            );
    }

}