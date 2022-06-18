<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Content\Authored\Paper;

use Pes\View\Renderer\ClassMap\ClassMapInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
class ElementEditableWrapper {

    /**
     *
     * @var ClassMapInterface
     */
    protected $classMap;

    /**
     *
     * @param ClassMapInterface $menuClassMap
     */
    public function __construct(ClassMapInterface $menuClassMap=NULL) {
        $this->classMap = $menuClassMap;
    }

    #### editable ###################

    /**
     * headline semafor a form
     *
     * @param MenuItemPaperAggregateInterface $paper
     * @return type
     */
    public function wrapHeadline(PaperInterface $paper) {
        return
            Html::tag('section', ['class'=>$this->classMap->get('Headline', 'section')],
                Html::tag(
                    'headline',
                    [
                        'id'=>"headline_{$paper->getId()}",  // id musí být na stránce unikátní - skládám ze slova headline_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->get('Headline', 'headline'),
                    ],
                    $paper->getHeadline() ?? ""
                )
            );
    }

    public function wrapPerex(PaperInterface $paper) {
        $form =
            Html::tag('section', ['class'=>$this->classMap->get('Perex', 'section')],
                Html::tag('form',
                    ['method'=>'POST', 'action'=>"red/v1/paper/{$paper->getId()}/perex"],
                    Html::tag('perex',
                        [
                            'id' => "perex_{$paper->getId()}",  // id musí být na stránce unikátní - skládám ze slova perex_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                            'class'=>$this->classMap->get('Perex', 'perex'),
                            'data-owner'=>$paper->getEditor(),
                            'data-text'=>'prázdný'
                        ],
                        $paper->getPerex() ?? ""
                        )
                )
            );
        return $form;
    }

    public function wrapContent(PaperContentInterface $paperContent) {
        /** @var PaperContentInterface $paperContent */
        if ($paperContent->getPriority() > 0) {  // není v koši
            $form = $this->getContentForm($paperContent);
        } else {  // je v koši
            $form = $this->getTrashContentForm($paperContent);
        }
        return $form;
    }

    private function getContentForm(PaperContentInterface $paperContent) {
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();
        $now =  new \DateTime("now");
        $future = $paperContent->getShowTime() > $now;
        $past = $paperContent->getHideTime() < $now;  // pro zobrszeno trvale - null je vždy menší a $passed je true - vyhodnucuji nejprve $actual, nevadí to

        return
            Html::tag('section', ['class'=>$this->classMap->get('Content', 'section')],
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'div.ribbon icon')])
                        .$this->getContentButtonsForm($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')],
                    Html::tag('i',
                       [
                       'class'=> $this->classMap->resolve($active, 'Icons','semafor.published', 'semafor.notpublished'),
                       'title'=> $active ? "published" : "not published",
                       ]
                    )
//                        'i2.published' => 'calendar check icon green',
//                        'i2.notactive' => 'calendar plus icon yellow',
//                        'i2.notactual' => 'calendar minus icon orange',
//                        'i2.notactivenotactual' => 'calendar times icon red',
                    .Html::tag('i',
                        [
                        'class'=> $this->classMap->resolve($actual, 'Content',
                                'i2.actual',
                                $past ?  'i2.past' : ($future ? 'i2.future' : 'i2.invalid')
                            ),
                        'role'=>"presentation",
                        'title'=> $actual ? 'actual' : $past ?  'past' : ($future ? 'future' : 'invalid dates')
                        ])
                    .$paperContent->getPriority()
                )
                .Html::tag('form',
                    [
                    'method'=>'POST',
                    'action'=>"red/v1/paper/{$paperContent->getPaperIdFk()}/content/{$paperContent->getId()}"
                    ],
                     Html::tag('content',
                        [
                        'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->get('Content', 'content'),
                        'data-owner'=>$paperContent->getEditor()
                        ],
                        $paperContent->getContent()
                    )
                )
            );
    }

    private function getTrashContentForm($paperContent) {
        return
            Html::tag('section', ['class'=>$this->classMap->get('Content', 'section.trash')],
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                    $this->getTrashButtonsForm($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')],
                        Html::tag('i',['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
                )
                .Html::tag('div',
                    [
                        'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->get('Content', 'div.trash_content'),
                        'data-owner'=>$paperContent->getEditor()
                    ],
                    $paperContent->getContent()
                    )
            );
    }

    protected function renderNewContent(PaperAggregatePaperContentInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('div', ['class'=>$this->classMap->get('Content', 'div div.ribbon')],
            $this->getNewContentButtonsForm($paperAggregate)
        )
        .Html::tag('form',
            ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/contents"],
            Html::tag('content',
                [
                    'id' => "new content_for_paper_$paperId",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMap->get('Content', 'form content'),
                    'data-paperowner'=>$paperAggregate->getEditor()
                ],
                "Nový obsah"
            )
        )
        ;
    }


    public function getContentButtonsForm(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $show = $paperContent->getShowTime();
        $hide = $paperContent->getHideTime();
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();

        if (isset($show)) {
            $showTime = $show->format("d.m.Y") ;
            if (isset($hide)) {
                $hideTime = $hide->format("d.m.Y");
                $textZobrazeni = "Zobrazeno od $showTime  do $hideTime";
            } else {
                $hideTime = '';
                $textZobrazeni = "Zobrazeno od $showTime";
            }
        } elseif (isset($hide)) {
            $showTime = '';
            $hideTime = $hide->format("d.m.Y");
            $textZobrazeni = "Zobrazeno do $hideTime";
        } else {
            $showTime = '';
            $hideTime = '';
            $textZobrazeni = "Zobrazeno trvale";
        }

        return

        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapContent')],
                Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Aktivní/neaktivní obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => 'toggle',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/toggle",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
                    )
                    .Html::tag('button', [
                        'class'=>$this->classMap->get('Buttons', 'button.date'),
                        'data-tooltip'=> $textZobrazeni,
                        'data-position'=>'top right',
                        'onclick'=>'event.preventDefault();'
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.changedate')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Posunout o jednu výš',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/up",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                            Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movecontent')])
                            .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowup')])
                        )
                    )
                    .Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Posunout o jednu níž',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/down",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                            Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movecontent')])
                            .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowdown')])
                        )
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Přidat další obsah před',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_above",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                            Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowup')])
                        )
                    )
                    .Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Přidat další obsah za',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_below",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                            Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowdown')])
                        )
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Do koše',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/trash",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsDate')],
                Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.permanently')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.save')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button.content'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'top right',
                    'onclick'=>"event.preventDefault(); this.form.reset();"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cancel')])
                )
                .Html::tag('div', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-position'=>'top right',
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.changedate')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapDate')],
                Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapKalendar'), ],
                        Html::tag('p', ['class'=>$this->classMap->get('Buttons', 'p')], 'Uveřejnit od')
                        .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.calendar')],
                            Html::tag('div',['class'=>$this->classMap->get('Buttons', 'div.input')],
                                Html::tagNopair('input', ['type'=>'text', 'name'=>'show', 'placeholder'=>'Klikněte pro výběr data', 'value'=>$showTime])
                            )
                         )
                        .Html::tag('p', ['class'=>$this->classMap->get('Buttons', 'p')], 'Uveřejnit do')
                        .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.calendar')],
                            Html::tag('div',['class'=>$this->classMap->get('Buttons', 'div.input')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>'hide', 'placeholder'=>'Klikněte pro výběr data', 'value'=> $hideTime])
                        )
                    )
                )
            )
        );
    }

    public function getTrashButtonsForm(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return

        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapTrash')],
                Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Obnovit',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/restore",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.restore')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Smazat',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/delete",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.delete')])
                    )
                )
            )

        );

    }

    public function getNewContentButtonsForm(PaperAggregatePaperContentInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.page')],
                Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperId/contents",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.icons')],
                            Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowdown')])
                        )
                    )
                )
            )
        );
    }
}
