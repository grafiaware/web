<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;

use Model\Entity\MenuNodeInterface;
use Model\Entity\PaperInterface;
use Pes\Text\Html;

/**
 * Description of HeadlinedEditableRenderer
 *
 * @author pes2704
 */
class HeadlinedEditableRenderer extends HtmlRendererAbstract {
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
            $headlineAtttributes =                         [
                            'id'=>"headline_{$paper->getMenuItemIdFk()}",
                            'class'=>$this->classMap->getClass('Component', 'div div div headline'),
                        ];
            $buttons = Html::tag('form', ['method'=>'POST', 'action'=>""],
                            $this->renderButtons($menuNode, $paper)
                        );
            $innerHtml =
                    $buttons
                    .Html::tag('form', ['method'=>'POST', 'action'=>"api/v1/paper/{$paper->getMenuItemIdFk()}"],
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
        } else {
            $innerHtml = Html::tag('div', [], 'No data item or article for rendering.');
        }
        // atribut data-component je jen pro info v html
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Component', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Component', 'div div')], $innerHtml)
            );
    }

    private function renderButtons(MenuNodeInterface $menuNode, PaperInterface $paper) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
            $showTime = $menuNode->getMenuItem()->getShowTime();
            $hideTime = $menuNode->getMenuItem()->getHideTime();
            if ($showTime) {
                if ($hideTime) {
                    $textZobrazeni = "Zobrazeno od $showTime  do $hideTime";
                } else {
                    $textZobrazeni = "Zobrazeno od $showTime";
                }
            } elseif ($hideTime) {
                    $textZobrazeni = "Zobrazeno do $hideTime";
            } else {
                    $textZobrazeni = "Zobrazeno trvale";
            }
            return
            Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.page')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Aktivní/neaktivní stránka',
                    'type'=>'submit',
                    'name'=>'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->resolveClass($menuNode->getMenuItem()->getActive(), 'Buttons', 'div button5 i.on', 'div button5 i.off')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button.date'),
                    'data-tooltip'=> $textZobrazeni,
                    'data-position'=>'top right',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div div i')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.date')],
                Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    //'name'=>'',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button6 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    //'name'=>'',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/{$menuNode->getUid()}/actual/$showTime/$hideTime",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button7 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button.page'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'top right',
                    //'type'=>'submit',
                    //'name'=>'',
                    //'formmethod'=>'post',
                    //'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    'onclick'=>"this.form.reset()"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button8 i')])
                )
                .Html::tag('div', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-position'=>'top right',
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div div i')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.date2')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div'), ],
                        Html::tag('p', ['class'=>$this->classMap->getClass('Buttons', 'div div div p')], 'Uveřejnit od')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div div')],
                            Html::tag('div',['class'=>$this->classMap->getClass('Buttons', 'div div div div div')],
                                Html::tagNopair('input', ['type'=>'text', 'name'=>'kalendarOD', 'placeholder'=>'Klikněte pro výběr data', 'value'=>$menuNode->getMenuItem()->getShowTime()])
                            )
                         )
                        .Html::tag('p', ['class'=>$this->classMap->getClass('Buttons', 'div div div p')], 'Uveřejnit do')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div div')],
                            Html::tag('div',['class'=>$this->classMap->getClass('Buttons', 'div div div div div')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>'kalendarDO', 'placeholder'=>'Klikněte pro výběr data', 'value'=> $menuNode->getMenuItem()->getHideTime()])
                        )
                    )
                )
            );
    }
}