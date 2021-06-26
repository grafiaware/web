<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlModelRendererAbstract;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Pes\View\Renderer\RendererModelAwareInterface;
use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
abstract class PaperRendererAbstract extends HtmlModelRendererAbstract {

    public function renderHeadline(PaperInterface $paper) {
        $headline = $paper->getHeadline();
        return
            Html::tag('div',
                        ['class'=>$this->classMap->getClass('Headline', 'div'),
                         'style' => "display: block;"
                        ],
                        Html::tag('headline',
                            ['class'=>$this->classMap->getClass('Headline', 'headline')],
                            $headline
                        )
                    );
    }

    public function renderPerex(PaperInterface $paper) {
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
    public function renderContents(PaperAggregatePaperContentInterface $paperAggregate) {
        $contents = $paperAggregate->getPaperContentsArraySorted(PaperAggregatePaperContentInterface::BY_PRIORITY);
        $innerHtml = '';
        foreach ($contents as $paperContent) {
            /** @var PaperContentInterface $paperContent */
            $innerHtml .= $this->renderContent($paperContent);
        }
        return $innerHtml;
    }

    public function renderContent(PaperContentInterface $paperContent) {
        return  Html::tag('content', [
                            'id' => "content_{$paperContent->getId()}",
                            'class'=>$this->classMap->getClass('Content', 'content'),
                            'data-owner'=>$paperContent->getEditor()
                        ],
                    $paperContent->getContent()
                );
    }

    #### editable ###################

    public function renderPaperButtonsForm(PaperInterface $paper) {
        $paperId = $paper->getId();

        $buttons = [];
        if ($paper instanceof PaperAggregatePaperContentInterface AND $paper->getPaperContentsArray()) {
            $buttons[] = Html::tag('button', [
                    'class'=>$this->classMapEditable->getClass('PaperButtons', 'button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('PaperButtons', 'button.arrange')])
                );
        } else {
            $buttons[] =  Html::tag('button',
                        ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperId/contents",
                        ],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    );
        }
        return Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperButtons', 'div.buttonsPage')],
                    implode('', $buttons)
            )
        );
    }
    /**
     * headline semafor a form
     *
     * @param MenuItemPaperAggregateInterface $paper
     * @return type
     */
    public function renderHeadlineEditable(PaperInterface $paper) {
        return
            Html::tag('section', ['class'=>$this->classMapEditable->getClass('Headline', 'section')],
                Html::tag(
                    'headline',
                    [
                        'id'=>"headline_{$paper->getId()}",  // id musí být na stránce unikátní - skládám ze slova headline_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMapEditable->getClass('Headline', 'headline'),
                    ],
                    Html::tag('div', ['class'=>"edit-text"], $paper->getHeadline() ?? "")
                )
            );
    }

    public function renderPerexEditable(PaperInterface $paper) {
        $form =
            Html::tag('section', ['class'=>$this->classMapEditable->getClass('Perex', 'section')],
                Html::tag('perex',
                    [
                        'id' => "perex_{$paper->getId()}",  // id musí být na stránce unikátní - skládám ze slova perex_ a paper id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMapEditable->getClass('Perex', 'perex'),
                        'data-owner'=>$paper->getEditor()
                    ],
                    Html::tag('div', ['class'=>"edit-html"], $paper->getPerex() ?? "")
                    )
            );
        return $form;
    }

    public function renderContentsEditable(PaperAggregatePaperContentInterface $paperAggregate) {
        $contents = $paperAggregate->getPaperContentsArraySorted(PaperAggregatePaperContentInterface::BY_PRIORITY);
        $sections = [];
        foreach ($contents as $paperContent) {
            /** @var PaperContentInterface $paperContent */
            if ($paperContent->getPriority() > 0) {  // není v koši
                $sections[] = $this->getContent($paperContent);
            } else {  // je v koši
                $sections[] = $this->getTrashContentForm($paperContent);
            }
        }
        return implode(PHP_EOL, $sections);
    }

    private function getContent(PaperContentInterface $paperContent) {
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();
        $now =  new \DateTime("now");
        $future = $paperContent->getShowTime() > $now;
        $past = $paperContent->getHideTime() < $now;  // pro zobrszeno trvale - null je vždy menší a $passed je true - vyhodnucuji nejprve $actual, nevadí to

        return
            Html::tag('section', ['class'=>$this->classMapEditable->getClass('Content', 'section')],
                Html::tag('div', ['class'=>$this->classMapEditable->getClass('Content', 'div.corner')],
                    $this->getContentButtons($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMapEditable->getClass('Content', 'div.semafor')],
                    Html::tag('i',
                       [
                       'class'=> $this->classMapEditable->resolveClass($active, 'Content','i1.published', 'i1.notpublished'),
                       'title'=> $active ? "published" : "not published",
                       ]
                    )
//                        'i2.published' => 'calendar check icon green',
//                        'i2.notactive' => 'calendar plus icon yellow',
//                        'i2.notactual' => 'calendar minus icon orange',
//                        'i2.notactivenotactual' => 'calendar times icon red',
                    .Html::tag('i',
                        [
                        'class'=> $this->classMapEditable->resolveClass($actual, 'Content',
                                'i2.actual',
                                $past ?  'i2.past' : ($future ? 'i2.future' : 'i2.invalid')
                            ),
                        'role'=>"presentation",
                        'title'=> $actual ? 'actual' : $past ?  'past' : ($future ? 'future' : 'invalid dates')
                        ])
                    .$paperContent->getPriority()
                )
                .Html::tag('content',
                    [
                    'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMapEditable->getClass('Content', 'content'),
                    'data-owner'=>$paperContent->getEditor()
                    ],
                    Html::tag('div', ['class'=>"edit-html"], $paperContent->getContent() ?? "")
                )
            );
    }

    private function getTrashContentForm($paperContent) {
        return
            Html::tag('section', ['class'=>$this->classMap->getClass('Content', 'section.trash')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.corner')],
                    $this->getTrashButtons($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')],
                        Html::tag('i',['class'=>$this->classMap->getClass('Content', 'i.trash')])
                )
                .Html::tag('div',
                    [
                        'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->getClass('Content', 'div.trash_content'),
                        'data-owner'=>$paperContent->getEditor()
                    ],
                    $paperContent->getContent()
                    )
            );
    }

    private function getContentButtons(PaperContentInterface $paperContent) {
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

        Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.wrapContent')],
            Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Aktivní/neaktivní obsah',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->resolveClass($active, 'ContentButtons', 'button.notpublish', 'button.publish')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMapEditable->getClass('ContentButtons', 'button.date'),
                    'data-tooltip'=> $textZobrazeni,
                    'data-position'=>'top right',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.changedate')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu výš',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/up",
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.arrowup')])
                    )
                )
                .Html::tag('button',
                    ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu níž',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/down",
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.arrowdown')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah před',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_above",
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.arrowup')])
                    )
                )
                .Html::tag('button',
                    ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah za',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_below",
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.arrowdown')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Do koše',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/trash",
                    ],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.movetotrash')])
                )
            )
        )
        .Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.buttonsDate')],
            Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                'data-tooltip'=>'Trvale',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'permanent',
                'formmethod'=>'post',
                'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.permanently')])
            )
            .Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                'data-tooltip'=>'Uložit',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'calendar',
                'formmethod'=>'post',
                'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.save')])
            )
            .Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('ContentButtons', 'button.content'),
                'data-tooltip'=>'Zrušit úpravy',
                'data-position'=>'top right',
                'onclick'=>"event.preventDefault(); this.form.reset();"
                ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.cancel')])
            )
            .Html::tag('div', [
                'class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                'data-position'=>'top right',
                ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.changedate')])
            )
        )
        .Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.wrapDate')],
            Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.wrapKalendar'), ],
                    Html::tag('p', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'p')], 'Uveřejnit od')
                    .Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.calendar')],
                        Html::tag('div',['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.input')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>"show_$paperContentId", 'placeholder'=>'Klikněte pro výběr data', 'value'=>$showTime])
                        )
                     )
                    .Html::tag('p', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'p')], 'Uveřejnit do')
                    .Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.calendar')],
                        Html::tag('div',['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.input')],
                        Html::tagNopair('input', ['type'=>'text', 'name'=>"hide_$paperContentId", 'placeholder'=>'Klikněte pro výběr data', 'value'=> $hideTime])
                    )
                )
            )
        );
    }

    private function getTrashButtons(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return
            Html::tag('div', ['class'=>$this->classMapEditable->getClass('TrashButtons', 'div.wrapTrash')],
                Html::tag('div', ['class'=>$this->classMapEditable->getClass('TrashButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMapEditable->getClass('TrashButtons', 'button'),
                        'data-tooltip'=>'Obnovit',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/restore",
                        ],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('TrashButtons', 'button.restore')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMapEditable->getClass('TrashButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMapEditable->getClass('TrashButtons', 'button'),
                        'data-tooltip'=>'Smazat',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/delete",
                        ],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('TrashButtons', 'button.delete')])
                    )
                )
            );

    }

    protected function renderNewContent(PaperAggregatePaperContentInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('div', ['class'=>$this->classMapEditable->getClass('Content', 'div div.corner')],
            $this->getNewContentButtonsForm($paperAggregate)
        )
        .Html::tag('form',
            ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/contents"],
            Html::tag('content',
                [
                    'id' => "new content_for_paper_$paperId",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMapEditable->getClass('Content', 'form content'),
                    'data-paperowner'=>$paperAggregate->getEditor()
                ],
                "Nový obsah"
            )
        )
        ;
    }

    private function getNewContentButtonsForm(PaperAggregatePaperContentInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperButtons', 'div.page')],
                Html::tag('div', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperId/contents",
                        ],
                        Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMapEditable->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    )
                )
            )
        );
    }
}
