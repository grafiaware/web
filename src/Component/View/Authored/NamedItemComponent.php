<?php
namespace Component\View\Authored;

use Component\View\ComponentAbstract;
use Component\ViewModel\Authored\Paper\NamedPaperViewModel;

use Pes\View\Renderer\RendererInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NamedItemComponent
 *
 * @author pes2704
 */
class NamedItemComponent extends AuthoredComponentAbstract implements NamedItemComponentInterface {


//    Segment:
//        příkladem je aktuality:
//        - aktuality mohou být jednoúrovňové - obsah je jeden article (případně do budoucna jiný typ obsahu) nebo dvouúrovňové
//            - jako každý article i aktuality je dostupní pomocí MenuItem, kreý obsahuje jako FK lankFk a uidFk -> foreing kea article
//            - MenuItem má m.j. vlastnosti actice, actual, start, stop - pokud nebude aktual nebo active nebude komponent mít žádný obsah
//            - v případě komponenty aktuality se tedy spíše hodí dvouúrovňová struktura v menu - první úroven slouží k nastavení active a
//              druhá úroveň k nastavení start - stop. Pak se v komponentě budou měnit jen položky. Vrchní položka pak může obsahovat titulek a
//              "perex"
//    - speciálně akce - zasložily by si nový typ - s datem konání, aby bylo možno řadit (možná i jiná kriteria)


    public function __construct(NamedPaperViewModel $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function setComponentName($componentName): NamedItemComponentInterface {
        $this->viewModel->setComponentName($componentName);
        return $this;
    }
}
