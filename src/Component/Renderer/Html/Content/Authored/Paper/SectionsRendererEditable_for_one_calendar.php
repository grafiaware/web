<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Content\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Entity\PaperSectionInterface;

use Pes\Text\Html;

/**
 * Description of ContentsRenderer
 *
 * @author pes2704
 */
class SectionsRendererEditable extends HtmlRendererAbstract {
    /**
     * Renderuje bloky s atributem id pro TinyMCE jméno proměnné ve formuláři
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $class
     * @return type
     */
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();
        if ($paperAggregate instanceof PaperAggregatePaperContentInterface) {

            $contents = $paperAggregate->getPaperContentsArraySorted(PaperAggregatePaperSectionInterface::BY_PRIORITY);
            $sections = [];
            foreach ($contents as $paperContent) {
                /** @var PaperSectionInterface $paperContent */
                if ($paperContent->getPriority() > 0) {  // není v koši
                    $sections[] = $this->getContent($paperContent);
                } else {  // je v koši
                    $sections[] = $this->getTrashContentForm($paperContent);
                }
            }

        } else {
            $sections[] = 'No content.';
        }
        return $sections;
    }

    private function getContent(PaperSectionInterface $paperContent) {
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();
        $now =  new \DateTime("now");
        $future = $paperContent->getShowTime() > $now;
        $past = $paperContent->getHideTime() < $now;  // pro zobrszeno trvale - null je vždy menší a $passed je true - vyhodnucuji nejprve $actual, nevadí to

        $html =
            Html::tag('section', ['class'=>$this->classMap->get('Content', 'section')],
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                    $this->getContentButtons($paperContent)
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
                    .Html::tag('span', ['class'=>''],
                        $paperContent->getPriority()
                    )
                )
                .Html::tag('content',
                    [
                    'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMap->get('Content', 'content'),
                    'data-owner'=>$paperContent->getEditor()
                    ],
                    $paperContent->getContent() ?? ""
                )
            );
        return $html;
    }

    private function getTrashContentForm($paperContent) {
        return
            Html::tag('section', ['class'=>$this->classMap->get('Content', 'section.trash')],
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                    $this->getTrashButtons($paperContent)
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

    private function getContentButtons(PaperSectionInterface $paperContent) {
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
        )
        .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapDate')],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapKalendar'), ],
                    Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.grid')],
                        Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.wholeRow')],
                            Html::tag('p', ['class'=>''], 'Uveřejnit obsah')
                        )
                        .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.halfRow')],
                            Html::tag('p', ['class'=>''], 'datum od')
                            .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.calendar')],
                                Html::tag('div',['class'=>$this->classMap->get('Content', 'div.input')],
                                    Html::tagNopair('input', ['type'=>'text', 'name'=>"show_$paperContentId", 'placeholder'=>'Klikněte pro výběr', 'value'=>$showTime])
                                )
                            )
                        )
                        .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.halfRow')],
                            Html::tag('p', ['class'=>''], 'datum do')
                            .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.calendar')],
                                Html::tag('div',['class'=>$this->classMap->get('Content', 'div.input')],
                                    Html::tagNopair('input', ['type'=>'text', 'name'=>"hide_$paperContentId", 'placeholder'=>'Klikněte pro výběr', 'value'=> $hideTime])
                                )
                            )
                        )
                        .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.wholeRow')],
                            Html::tag('p', ['class'=>''], 'Nastavit datum události')
                        )
                        .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.halfRow')],
                            Html::tag('p', ['class'=>''], 'datum od')
                            .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.calendar')],
                                Html::tag('div',['class'=>$this->classMap->get('Content', 'div.input')],
                                    Html::tagNopair('input', ['type'=>'text', 'name'=>"", 'placeholder'=>'Klikněte pro výběr', 'value'=>''])
                                )
                            )
                        )
                        .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.halfRow')],
                            Html::tag('p', ['class'=>''], 'datum do')
                            .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.calendar')],
                                Html::tag('div',['class'=>$this->classMap->get('Content', 'div.input')],
                                    Html::tagNopair('input', ['type'=>'text', 'name'=>"", 'placeholder'=>'Klikněte pro výběr', 'value'=> ''])
                                )
                            )
                        )
                    )
            )
        );
    }

    private function getTrashButtons(PaperSectionInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return
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
            );

    }
}
