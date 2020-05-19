<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Model\Entity\MenuNodeInterface;
use Model\Entity\PaperInterface;
use Model\Entity\PaperHeadlineInterface;
use Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
abstract class AuthoredEditableRendererAbstract extends HtmlRendererAbstract {

    protected function renderButtonsForm(MenuNodeInterface $menuNode) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $show = $menuNode->getMenuItem()->getShowTime();
        $hide = $menuNode->getMenuItem()->getHideTime();


        if (isset($show)) {
            $showTime = $show;//->format("d.m.Y") ;
            if (isset($hide)) {
                $hideTime = $hide;//->format("d.m.Y");
                $textZobrazeni = "Zobrazeno od $showTime  do $hideTime";
            } else {
                $hideTime = '0';
                $textZobrazeni = "Zobrazeno od $showTime";
            }
        } elseif (isset($hide)) {
            $showTime = '0';
            $hideTime = $hide;//->format("d.m.Y");
            $textZobrazeni = "Zobrazeno do $hideTime";
        } else {
            $showTime = '0';
            $hideTime = '0';
            $textZobrazeni = "Zobrazeno trvale";
        }

        return

        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.page')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Aktivní/neaktivní stránka',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'toggle',
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
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/{$menuNode->getUid()}/actual/",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button6 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/{$menuNode->getUid()}/actual/",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button7 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button.page'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'top right',
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
                                Html::tagNopair('input', ['type'=>'text', 'name'=>'show', 'placeholder'=>'Klikněte pro výběr data', 'value'=>$menuNode->getMenuItem()->getShowTime()])
                            )
                         )
                        .Html::tag('p', ['class'=>$this->classMap->getClass('Buttons', 'div div div p')], 'Uveřejnit do')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div div')],
                            Html::tag('div',['class'=>$this->classMap->getClass('Buttons', 'div div div div div')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>'hide', 'placeholder'=>'Klikněte pro výběr data', 'value'=> $menuNode->getMenuItem()->getHideTime()])
                        )
                    )
                )
            )
        );
    }

    protected function renderContentsForms(PaperInterface $paper) {
        foreach ($paper->getPaperContentsArray() as $id => $paperContent) {
            /** @var PaperContentInterface $paperContent */
            $form[] =
                Html::tag('form',
                    ['method'=>'POST', 'action'=>"api/v1/paper/{$paperContent->getMenuItemIdFk()}/content/{$paperContent->getId()}"],
                    Html::tag('content',
                        [
                            'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lte toto jméno také složit a hledat v POST proměnných
                            'class'=>$this->classMap->getClass('Content', 'form content'),
                            'data-owner'=>$paperContent->getEditor()
                        ],
                        $paperContent->getContent()
                    )
                );
        }
        return implode(PHP_EOL, $form);
    }

    protected function renderHeadlineForm(PaperInterface $paper, $active, $actual) {
        $paperHeadline = $paper->getPaperHeadline();
        return Html::tag('form', ['method'=>'POST', 'action'=>"api/v1/paper/{$paperHeadline->getMenuItemIdFk()}/headline/"],
                        Html::tag('div', ['class'=>$this->classMap->getClass('Headline', 'form div')],
                            Html::tag(
                                'headline',
                                [
                                    'id'=>"headline_{$paperHeadline->getMenuItemIdFk()}",  // id musí být na stránce unikátní - skládám ze slova headline_ a MenuItemIdFk, v kontroléru lte toto jméno také složit a hledat v POST proměnných
                                    'class'=>$this->classMap->getClass('Headline', 'form div headline'),
                                ],
                                $paperHeadline->getHeadline()
                            )
                            .Html::tag('i',
                                ['class'=> $this->classMap->resolveClass(($active AND $actual), 'Headline',
                                    'form div i1.published', 'form div i1.notpublished')
                                ]
                            )
                            .Html::tag('i',
                                ['class'=> $this->classMap->resolveClass($active, 'Headline',
                                    $actual ? 'form div i2.published' : 'form div i2.notactual',
                                    $actual ?  'form div i2.notactive' : 'form div i2.notactivenotactual')
                                ]
                            )
                            //.Html::tag('i', ['class'=>$this->classMap->getClass('Component', 'div div div i3')])
                        )
                    );
    }
}
