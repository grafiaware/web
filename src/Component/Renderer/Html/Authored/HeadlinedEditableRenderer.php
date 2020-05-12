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
class HeadlinedEditableRenderer extends AuthoredEditableRenderer {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $menuNode = $viewModel->getMenuNode();
        $paper = $viewModel->getPaper();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponent()->getName();
        } else {
            $name = "presented";
        }

        if (isset($menuNode) AND isset($paper)) {

            // TinyMCE v inline režimu pojmenovává proměnné v POSTu mce_XX, kde XX je asi pořadové číslo selected elementu na celé stránce
            // takže to znamená buď používat form tak, že obsahuje vždy jen jednu proměnnou a pak mce_cokoliv jsou zaslaná data
            // nebo přidat editovanému elementu id, pak TinyMCE použije toto id. Použije ho tak, že přidá tag <input type=hidden name=id_editovaného_elementu> a této proměnné přiřadí hodnotu.
            // Jenže id elementu musí být unikátní na stránce, proto přidávám paper id -> na serveru pak se dá hledané jméno proměnné v postu složit ze stringu "content_"
            // a parametru v rest, který obsahuje paper id.


//                                 'onblur'=>'var throw=confirm("Chcete zahodit změny v obsahu headline?"); if (throw==false) {document.getElementByName("headline").focus(); }'
            $headlineAtttributes = [
                            'id'=>"headline_{$paper->getMenuItemIdFk()}",
                            'class'=>$this->classMap->getClass('Component', 'div div div headline'),
                        ];
            $buttonsForm =  $this->renderButtons($menuNode, $paper);

            $paperForm =
                    Html::tag('form', ['method'=>'POST', 'action'=>"api/v1/paper/{$paper->getMenuItemIdFk()}"],
                        Html::tag('div', ['class'=>$this->classMap->getClass('Component', 'div div div')],
                            Html::tag(
                                'headline',
                                $headlineAtttributes,
                                $paper->getHeadline()
                            )
                            .Html::tag('i', ['class'=> $this->classMap->resolveClass(($menuNode->getMenuItem()->getActive() AND $menuNode->getMenuItem()->getActual()), 'Component',
                                    'div div div i1.published', 'div div div i1.notpublished')]
                            )
                            .Html::tag('i', ['class'=> $this->classMap->resolveClass($menuNode->getMenuItem()->getActive(), 'Component',
                                    $menuNode->getMenuItem()->getActual() ? 'div div div i2.published' : 'div div div i2.notactual',
                                    $menuNode->getMenuItem()->getActual() ?  'div div div i2.notactive' : 'div div div i2.notactivenotactual'
                            )])
                            //.Html::tag('i', ['class'=>$this->classMap->getClass('Component', 'div div div i3')])
                        )
                        .Html::tag('content', ['id'=>"content_{$paper->getMenuItemIdFk()}", 'class'=>$this->classMap->getClass('Component', 'div div content')], $paper->getContent())
                    );
            $innerHtml = $buttonsForm.$paperForm;
        } else {
            $innerHtml = Html::tag('div', [], 'Missing data item or article for rendering.');
        }
        // atribut data-component je jen pro info v html
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Component', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Component', 'div div')], $innerHtml)
            );
    }

}