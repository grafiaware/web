<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Model\Entity\HierarchyNodeInterface;
use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\PaperHeadlineInterface;
use Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
abstract class AuthoredEditableRendererAbstract extends HtmlRendererAbstract {

    protected function renderPaperButtonsForm(MenuItemPaperAggregateInterface $paper) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $uid = $paper->getUidFk();
        $active = $paper->getActive();
        return

        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.page')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('PaperButtons', 'div button'),
                    'data-tooltip'=>'Aktivní/neaktivní stránka',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/$uid/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->resolveClass($active, 'PaperButtons', 'div button1 i.on', 'div button1 i.off')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('PaperButtons', 'div button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperButtons', 'div button2 i')])
                )
            )
        );
    }

    protected function renderContentButtonsForm(MenuItemPaperAggregateInterface $paper) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $show = $paper->getShowTime();
        $hide = $paper->getHideTime();
        $uid = $paper->getUidFk();
        $active = $paper->getActive();
        $actual = $paper->getActual();

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
            Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div div.content')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'div div button'),
                        'data-tooltip'=>'Aktivní/neaktivní stránka',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => 'toggle',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/menu/$uid/toggle",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->resolveClass($active, 'ContentButtons', 'div div button1 i.on', 'div div button1 i.off')])
                    )
                    .Html::tag('button', [
                        'class'=>$this->classMap->getClass('ContentButtons', 'div div button.date'),
                        'data-tooltip'=> $textZobrazeni,
                        'data-position'=>'top right',
                        'onclick'=>'event.preventDefault();'
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button2 i')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div div.content')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'div div button'),
                        'data-tooltip'=>'Posunout o jednu výš',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.group')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.note')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.arrowup')])
                        )
                    )
                    .Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'div div button'),
                        'data-tooltip'=>'Posunout o jednu níž',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.group')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.note')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.arrowdown')])
                        )
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div div.content')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'div div button'),
                        'data-tooltip'=>'Přidat další obsah před',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.group')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.square')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.arrowup')])
                        )
                    )
                    .Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'div div button'),
                        'data-tooltip'=>'Přidat další obsah za',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.group')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.square')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button i.arrowdown')])
                        )
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div div.content')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'div div button'),
                        'data-tooltip'=>'Smazat',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button7 i')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.date')],
                Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'div button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/$uid/actual/",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div button16 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'div button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/$uid/actual/",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div button17 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'div button.content'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'top right',
                    'onclick'=>"this.form.reset()"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div button18 i')])
                )
                .Html::tag('div', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'div button'),
                    'data-position'=>'top right',
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'div div button2 i')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.date2')],
                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div div div'), ],
                        Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'div div div p')], 'Uveřejnit od')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div div div div')],
                            Html::tag('div',['class'=>$this->classMap->getClass('ContentButtons', 'div div div div div')],
                                Html::tagNopair('input', ['type'=>'text', 'name'=>'show', 'placeholder'=>'Klikněte pro výběr data', 'value'=>$show])
                            )
                         )
                        .Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'div div div p')], 'Uveřejnit do')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div div div div')],
                            Html::tag('div',['class'=>$this->classMap->getClass('ContentButtons', 'div div div div div')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>'hide', 'placeholder'=>'Klikněte pro výběr data', 'value'=> $hide])
                        )
                    )
                )
            )
        );
    }

    protected function renderContentsDivs(MenuItemPaperAggregateInterface $paper) {
        $active = $paper->getActive();
        $actual = $paper->getActual();

        foreach ($paper->getPaperContentsArray() as $id => $paperContent) {
            /** @var PaperContentInterface $paperContent */
            $form[] =
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div')],
                    Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div div')],
                            Html::tag('i',
                                ['class'=> $this->classMap->resolveClass(($active AND $actual), 'Content',
                                    'div div i1.published', 'div div i1.notpublished')
                                ]
                            )
                            .Html::tag('i',
                                ['class'=> $this->classMap->resolveClass($active, 'Content',
                                    $actual ? 'div div i2.published' : 'div div i2.notactual',
                                    $actual ? 'div div i2.notactive' : 'div div i2.notactivenotactual')
                                ]
                            )
                    )
                    .Html::tag('form',
                        ['method'=>'POST', 'action'=>"api/v1/paper/{$paperContent->getMenuItemIdFk()}/content/{$paperContent->getId()}"],
                        Html::tag('content',
                            [
                                'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lte toto jméno také složit a hledat v POST proměnných
                                'class'=>$this->classMap->getClass('Content', 'form content'),
                                'data-owner'=>$paperContent->getEditor()
                            ],
                            $paperContent->getContent()
                            )
                    )
                    .$this->renderContentButtonsForm($paper)
                );
        }
        return implode(PHP_EOL, $form);
    }

    /**
     * headline semafor a form
     *
     * @param MenuItemPaperAggregateInterface $paper
     * @return type
     */
    protected function renderHeadlineForm(MenuItemPaperAggregateInterface $paper) {
        $active = $paper->getActive();
        $actual = $paper->getActual();
        $paperHeadline = $paper->getPaperHeadline();
        return
            Html::tag('div', ['class'=>$this->classMap->getClass('Headline', 'div')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Headline', 'div div')],
                    Html::tag('i',
                        ['class'=> $this->classMap->resolveClass(($active AND $actual), 'Headline',
                            'div div i1.published', 'div div i1.notpublished')
                        ]
                    )
                    .Html::tag('i',
                        ['class'=> $this->classMap->resolveClass($active, 'Headline',
                            $actual ? 'div div i2.published' : 'div div i2.notactual',
                            $actual ?  'div div i2.notactive' : 'div div i2.notactivenotactual')
                        ]
                    )
                )
                .Html::tag('form', ['method'=>'POST', 'action'=>"api/v1/paper/{$paperHeadline->getMenuItemIdFk()}/headline/"],
                    Html::tag(
                        'headline',
                        [
                            'id'=>"headline_{$paperHeadline->getMenuItemIdFk()}",  // id musí být na stránce unikátní - skládám ze slova headline_ a MenuItemIdFk, v kontroléru lte toto jméno také složit a hledat v POST proměnných
                            'class'=>$this->classMap->getClass('Headline', 'form headline'),
                        ],
                        $paperHeadline->getHeadline()
                    )
                )
            );
    }
}
