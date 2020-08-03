<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Model\Entity\PaperAggregateInterface;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
abstract class PaperEditableRendererAbstract  extends AuthoredEditableRendererAbstract {

    protected function renderPaper(PaperAggregateInterface $paperAggregate) {

            // TinyMCE v inline režimu pojmenovává proměnné v POSTu mce_XX, kde XX je asi pořadové číslo selected elementu na celé stránce, pokud editovaný element má id, pak TinyMCE použije toto id.
            // Použije ho tak, že přidá tag <input type=hidden name=id_editovaného_elementu> a této proměnné přiřadí hodnotu.
            // Samozřejmě id elementu musí být unikátní na stránce.

//                'onblur'=>'var throw=confirm("Chcete zahodit změny v obsahu headline?"); if (throw==false) {document.getElementByName("headline").focus(); }'

        return
                $this->renderPaperButtonsForm($paperAggregate).
                $this->renderHeadlineForm($paperAggregate).
                $this->renderPerexForm($paperAggregate).
                $this->renderContentsDivs($paperAggregate).
                    ""
                ;
    }
}

