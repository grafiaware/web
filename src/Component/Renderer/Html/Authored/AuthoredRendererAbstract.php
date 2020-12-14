<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Model\Entity\PaperAggregateInterface;
use Model\Entity\PaperInterface;
use Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
abstract class AuthoredRendererAbstract extends HtmlRendererAbstract {


    protected function renderHeadline(PaperAggregateInterface $headline) {
        $headline = $headline->getHeadline();
        return  $headline
                ?
                Html::tag('div',
                    ['class'=>$this->classMap->getClass('Headline', 'div')],
                    Html::tag('headline',
                            ['class'=>$this->classMap->getClass('Headline', 'headline')],
                            $headline
                    )
                )
                :
                ""
            ;
    }

    protected function renderPerex(PaperAggregateInterface $paper) {
        $perex = $paper->getPerex();
        return  $perex
                ?
                Html::tag('perex',
                    ['class'=>$this->classMap->getClass('Perex', 'perex')],
                    $perex
                )
                :
                ""
                ;
    }


    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $class
     * @return type
     */
    protected function renderContents(PaperAggregateInterface $paperAggregate) {
        $contents = $paperAggregate->getPaperContentsArraySorted(PaperAggregateInterface::BY_PRIORITY);
        $innerHtml = '';
        foreach ($contents as $paperContent) {
            /** @var PaperContentInterface $paperContent */
            $innerHtml .=
                Html::tag('content', [
                            'id' => "content_{$paperContent->getId()}",
                            'class'=>$this->classMap->getClass('Content', 'content'),
                            'data-owner'=>$paperContent->getEditor()
                        ],
                    $paperContent->getContent()
                );
        }
        return $innerHtml;
    }

    #### editable ###################

    protected function renderPaperTemplateButtonsForm(PaperAggregateInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();
        $paperTemplatesName = $paperAggregate->getTemplate();
        $selectHtml = '';
//        foreach ($paperTemplatesName as $type) {
//            $selectHtml .=  Html::tag('div', ['value'=>$type->getType(), 'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.item')],
//                                $type->getType()
//                            );
//
//        }
        return
            Html::tag('form', ['method'=>'POST', 'action'=>"api/v1/paper/$paperId/template"],
                Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateButtons', 'div.paperTemplate'), 'data-tooltip'=>'Výběr šablony stránky'],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperTemplateButtons', 'button.templateSelect')])
                    .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.menu')],
                        Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.header')], 'Vyberte šablonu stránky')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.selection')],
                            Html::tag('input', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'input'), 'type'=>'hidden', 'name'=>'select', 'onchange'=>'this.form.submit()'] )
                            .Html::tag('i', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'i.dropdown')])
                            .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.text')], 'Šablona')
                            .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.scrollmenu')],
                                    Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.item'), 'value'=>'course'], 'Course')
                                    .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.item'), 'value'=>'contact'], 'Contact')
                                    .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.item'), 'value'=>'default'], 'Výchozí')
                            )
                        )
                    )
                )
            );
    }

    protected function renderPaperButtonsForm(PaperAggregateInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        $buttons = [];
        if ($paperAggregate->getPaperContentsArray()) {
            $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->getClass('PaperButtons', 'button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperButtons', 'button.arrange')])
                );
        } else {
            $buttons[] =  Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperId/contents",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    );
        }
        return Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.buttonsPage')],
                    implode('', $buttons)
            )
        );
    }
    /**
     * headline semafor a form
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @return type
     */
    protected function renderHeadlineForm(PaperAggregateInterface $paperAggregate) {
        return
            Html::tag('section', ['class'=>$this->classMap->getClass('Headline', 'section')],
                    Html::tag('form', ['method'=>'POST', 'action'=>"api/v1/paper/{$paperAggregate->getId()}/headline"],
                    Html::tag(
                        'headline',
                        [
                            'id'=>"headline_{$paperAggregate->getId()}",  // id musí být na stránce unikátní - skládám ze slova headline_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                            'class'=>$this->classMap->getClass('Headline', 'headline'),
                        ],
                        $paperAggregate->getHeadline() ?? ""
                    )
                )
            );
    }

    protected function renderPerexForm(PaperAggregateInterface $paperAggregate) {
        $form =
            Html::tag('section', ['class'=>$this->classMap->getClass('Perex', 'section')],
                Html::tag('form',
                    ['method'=>'POST', 'action'=>"api/v1/paper/{$paperAggregate->getId()}/perex"],
                    Html::tag('perex',
                        [
                            'id' => "perex_{$paperAggregate->getId()}",  // id musí být na stránce unikátní - skládám ze slova perex_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                            'class'=>$this->classMap->getClass('Perex', 'perex'),
                            'data-owner'=>$paperAggregate->getEditor(),
                            'data-text'=>'prázdný'
                        ],
                        $paperAggregate->getPerex() ?? ""
                        )
                )
            );
        return $form;
    }

    protected function renderContentsDivs(PaperAggregateInterface $paperAggregate) {
        $contents = $paperAggregate->getPaperContentsArraySorted(PaperAggregateInterface::BY_PRIORITY);
        $form = [];
        foreach ($contents as $paperContent) {
            /** @var PaperContentInterface $paperContent */
            if ($paperContent->getPriority() > 0) {  // není v koši
                $form[] = $this->getContentForm($paperContent, $paperAggregate);
            } else {  // je v koši
                $form[] = $this->getTrashContentForm($paperContent, $paperAggregate);
            }
        }
        return implode(PHP_EOL, $form);
    }

    private function getContentForm(PaperContentInterface $paperContent, PaperAggregateInterface $paperAggregate) {
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();
        $now =  new \DateTime("now");
        $future = $paperContent->getShowTime() > $now;
        $past = $paperContent->getHideTime() < $now;  // pro zobrszeno trvale - null je vždy menší a $passed je true - vyhodnucuji nejprve $actual, nevadí to

        return
            Html::tag('section', ['class'=>$this->classMap->getClass('Content', 'section')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.corner')],
                    $this->getContentButtonsForm($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')],
                    Html::tag('i',
                       [
                       'class'=> $this->classMap->resolveClass($active, 'Content','i1.published', 'i1.notpublished'),
                       'title'=> $active ? "published" : "not published",
                       ]
                    )
//                        'i2.published' => 'calendar check icon green',
//                        'i2.notactive' => 'calendar plus icon yellow',
//                        'i2.notactual' => 'calendar minus icon orange',
//                        'i2.notactivenotactual' => 'calendar times icon red',
                    .Html::tag('i',
                        [
                        'class'=> $this->classMap->resolveClass($actual, 'Content',
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
                    'action'=>"api/v1/paper/{$paperContent->getPaperIdFk()}/contents/{$paperContent->getId()}"
                    ],
                     Html::tag('content',
                        [
                        'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->getClass('Content', 'content'),
                        'data-paperowner'=>$paperAggregate->getEditor(),
                        'data-owner'=>$paperContent->getEditor()
                        ],
                        $paperContent->getContent()
                    )
                )
            );
    }

    private function getTrashContentForm($paperContent, $paperAggregate) {
        return
            Html::tag('section', ['class'=>$this->classMap->getClass('Content', 'section.trash')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.corner')],
                    $this->getTrashButtonsForm($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')],
                        Html::tag('i',['class'=>$this->classMap->getClass('Content', 'i.trash')])
                )
                .Html::tag('div',
                    [
                        'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->getClass('Content', 'div.trash_content'),
                        'data-paperowner'=>$paperAggregate->getEditor(),
                        'data-owner'=>$paperContent->getEditor()
                    ],
                    $paperContent->getContent()
                    )
            );
    }

    private function getContentButtonsForm(PaperContentInterface $paperContent) {
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
            Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapContent')],
                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Aktivní/neaktivní stránka',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => 'toggle',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/toggle",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->resolveClass($active, 'ContentButtons', 'button.notpublish', 'button.publish')])
                    )
                    .Html::tag('button', [
                        'class'=>$this->classMap->getClass('ContentButtons', 'button.date'),
                        'data-tooltip'=> $textZobrazeni,
                        'data-position'=>'top right',
                        'onclick'=>'event.preventDefault();'
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.changedate')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Posunout o jednu výš',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/up",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movecontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowup')])
                        )
                    )
                    .Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Posunout o jednu níž',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/down",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movecontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat další obsah před',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/add_above",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowup')])
                        )
                    )
                    .Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat další obsah za',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/add_below",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Smazat',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/trash",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movetotrash')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsDate')],
                Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.permanently')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.save')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button.content'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'top right',
                    'onclick'=>"event.preventDefault(); this.form.reset();"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.cancel')])
                )
                .Html::tag('div', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-position'=>'top right',
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.changedate')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapDate')],
                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapKalendar'), ],
                        Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'p')], 'Uveřejnit od')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.calendar')],
                            Html::tag('div',['class'=>$this->classMap->getClass('ContentButtons', 'div.input')],
                                Html::tagNopair('input', ['type'=>'text', 'name'=>'show', 'placeholder'=>'Klikněte pro výběr data', 'value'=>$showTime])
                            )
                         )
                        .Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'p')], 'Uveřejnit do')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.calendar')],
                            Html::tag('div',['class'=>$this->classMap->getClass('ContentButtons', 'div.input')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>'hide', 'placeholder'=>'Klikněte pro výběr data', 'value'=> $hideTime])
                        )
                    )
                )
            )
        );
    }

    private function getTrashButtonsForm(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return

        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.wrapTrash')],
                Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('TrashButtons', 'button'),
                        'data-tooltip'=>'Obnovit',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/restore",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('TrashButtons', 'button.restore')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('TrashButtons', 'button'),
                        'data-tooltip'=>'Smazat',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperIdFk/contents/$paperContentId/delete",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('TrashButtons', 'button.delete')])
                    )
                )
            )

        );

    }

    protected function renderNewContent(PaperAggregateInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div div.corner')],
            $this->getNewContentButtonsForm($paperAggregate)
        )
        .Html::tag('form',
            ['method'=>'POST', 'action'=>"api/v1/paper/$paperId/contents"],
            Html::tag('content',
                [
                    'id' => "new content_for_paper_$paperId",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMap->getClass('Content', 'form content'),
                    'data-paperowner'=>$paperAggregate->getEditor()
                ],
                "Nový obsah"
            )
        )
        ;
    }

    private function getNewContentButtonsForm(PaperAggregateInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.page')],
                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperId/contents",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    )
                )
            )
        );
    }
}
