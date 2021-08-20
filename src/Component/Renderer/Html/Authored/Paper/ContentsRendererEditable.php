<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of ContentsRenderer
 *
 * @author pes2704
 */
class ContentsRendererEditable extends HtmlRendererAbstract {
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

        } else {
            $sections[] = 'No content.';
        }
        return $sections;
    }

    private function getContent(PaperContentInterface $paperContent) {
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();
        $now =  new \DateTime("now");
        $future = $paperContent->getShowTime() > $now;
        $past = $paperContent->getHideTime() < $now;  // pro zobrszeno trvale - null je vždy menší a $passed je true - vyhodnucuji nejprve $actual, nevadí to

        $html =
            Html::tag('section', ['class'=>$this->classMap->getClass('Content', 'section')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.corner')],
                    $this->getContentButtons($paperContent)
                )
                    .'<div class="div-osa" data-tooltip="">
                            <div class="zelene-obdobi">
                                  <div class="cislo1"></div>
                                  <div class="cislo2"></div>
                            </div>
                            <div class="dnes" data-datumOsa="'.$future.'x,'.$past.'x"></div>
                      </div>'
//                .Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')],
//                    Html::tag('i',
//                       [
//                       'class'=> $this->classMap->resolveClass($active, 'Content','i1.published', 'i1.notpublished'),
//                       'title'=> $active ? "published" : "not published",
//                       ]
//                    )
//                    .Html::tag('i',
//                        [
//                        'class'=> $this->classMap->resolveClass($actual, 'Content',
//                                'i2.actual',
//                                $past ?  'i2.past' : ($future ? 'i2.future' : 'i2.invalid')
//                            ),
//                        'role'=>"presentation",
//                        'title'=> $actual ? 'actual' : $past ?  'past' : ($future ? 'future' : 'invalid dates')
//                        ])
//                    .Html::tag('span', ['class'=>''],
//                        $paperContent->getPriority()
//                    )
//                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')],
                    Html::tag('div',
                       [
                       'class'=> 'ikona-popis',
                       'data-tooltip'=> $active ? "published" : "not published",
                       ],
                        Html::tag('i',
                           [
                           'class'=> $this->classMap->resolveClass($active, 'Content','i1.published', 'i1.notpublished'),
                           ]
                        )
                    )
                    .Html::tag('div',
                        [
                        'class'=> 'ikona-popis',
                        'role'=>"presentation",
                        'data-tooltip'=> $actual ? 'actual' : $past ?  'past' : ($future ? 'future' : 'invalid dates')
                        ],
                        Html::tag('i',
                            [
                            'class'=> $this->classMap->resolveClass($actual, 'Content',
                                    'i2.actual',
                                    $past ?  'i2.past' : ($future ? 'i2.future' : 'i2.invalid')
                                ),
                            ])
                    )
                    .Html::tag('span', ['class'=>''],
                        $paperContent->getPriority()
                    )
                )
                .Html::tag('content',
                    [
                    'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMap->getClass('Content', 'content'),
                    'data-owner'=>$paperContent->getEditor()
                    ],
                    $paperContent->getContent() ?? ""
                )
            );
        return $html;
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
        $showTime = $paperContent->getShowTime();
        $hideTiem = $paperContent->getHideTime();
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();

        if (isset($showTime)) {
            $showTimeText = $showTime->format("d.m.Y") ;
            if (isset($hideTiem)) {
                $hideTimeText = $hideTiem->format("d.m.Y");
                $textDatumyZobrazeni = "Zobrazeno od $showTimeText  do $hideTimeText";
            } else {
                $hideTimeText = '';
                $textDatumyZobrazeni = "Zobrazeno od $showTimeText";
            }
        } elseif (isset($hideTiem)) {
            $showTimeText = '';
            $hideTimeText = $hideTiem->format("d.m.Y");
            $textDatumyZobrazeni = "Zobrazeno do $hideTimeText";
        } else {
            $showTimeText = '';
            $hideTimeText = '';
            $textDatumyZobrazeni = "Zobrazeno trvale";
        }
        $eventStartTime = $paperContent->getEventStartTime();
        $eventEndTime = $paperContent->getEventEndTime();
        $eventStartTimeText = isset($eventStartTime) ? $eventStartTime->format("d.m.Y") : '';
        $eventEndTimeText = isset($eventEndTime) ? $eventEndTime->format("d.m.Y") : '';
        if (isset($eventStartTime)) {
            $eventStartTimeText = $eventStartTime->format("d.m.Y") ;
            if (isset($eventEndTime)) {
                $eventEndTimeText = $eventEndTime->format("d.m.Y");
                $textDatumyUdalosti = "Událost se koná od $eventStartTimeText  do $eventEndTimeText";
            } else {
                $eventEndTimeText = '';
                $textDatumyUdalosti = "Událost se koná od $eventStartTimeText";
            }
        } elseif (isset($eventEndTime)) {
            $eventStartTimeText = '';
            $eventEndTimeText = $eventEndTime->format("d.m.Y");
            $textDatumyUdalosti = "Událost se koná do $eventEndTimeText";
        } else {
            $eventStartTimeText = '';
            $eventEndTimeText = '';
            $textDatumyUdalosti = "Událost se koná trvale";
        }

        $btnAktivni =                 Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Aktivní/neaktivní obsah',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->resolveClass($active, 'ContentButtons', 'button.notpublish', 'button.publish')])
                );
        $btnDoKose = Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Do koše',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/trash",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movetotrash')])
                );
        $btnDatumyZobrazeni = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button.showDate'),
                    'data-tooltip'=> $textDatumyZobrazeni,
                    'data-position'=>'top right',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.changedisplaydate')])
                );
            ## buttony datumy zobrazeni
            $btnZobrazeniTrvale = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.permanently')])
                );
            $btnZobrazeniUlozit = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.save')])
                );
            $btnZobrazeniZrusitUpravy = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button.content'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'top right',
                    'onclick'=>"event.preventDefault(); this.form.reset();"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.cancel')])
            );
        $btnDatumyUdalosti = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button.eventDate'),
                    'data-tooltip'=> $textDatumyUdalosti,
                    'data-position'=>'top right',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.changeeventdate')])
                );
            ## buttony datumy udalost
            $btnUdalostTrvale = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/event",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.permanently')])
                );
            $btnUdalostUlozit = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'top right',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/event",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.save')])
                );
            $btnUdalostZrusitUpravy = Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button.content'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'top right',
                    'onclick'=>"event.preventDefault(); this.form.reset();"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.cancel')])
                );
        $btnOJednuVys = Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu výš',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/up",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowup')])
                    )
                );
        $btnOJednuNiz = Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu níž',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/down",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                    )
                );
        $btnPridatObsahPred =                 Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah před',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_above",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowup')])
                    )
                );
        $btnPridatObsahZa = Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah za',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_below",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                    )
                );


        return

        Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapContent')],
            Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    $btnAktivni.$btnDoKose
                    )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    $btnDatumyZobrazeni.$btnDatumyUdalosti
                    )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    $btnOJednuVys.$btnOJednuNiz
                    )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    $btnPridatObsahPred.$btnPridatObsahZa
            )
        )
        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapShowDate')],
            Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsEditShowDate')],
                $btnZobrazeniTrvale.$btnZobrazeniUlozit.$btnZobrazeniZrusitUpravy
             )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.grid')],

                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wholeRow')],
                    Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'p')], 'Uveřejnit obsah')
                )
                .$this->calendar('datum od', "show_$paperContentId", 'Klikněte pro výběr', $showTimeText)
                .$this->calendar('datum do', "hide_$paperContentId", 'Klikněte pro výběr', $hideTimeText)

            )
        )
        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapEventDate')],       // display none <-> display flex
            Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsEditEventDate')],
                $btnUdalostTrvale.$btnUdalostUlozit.$btnUdalostZrusitUpravy
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.grid')],

                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wholeRow')],
                    Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'p')], 'Nastavit datum události')
                )
                .$this->calendar('datum od', "start_$paperContentId", 'Klikněte pro výběr', $eventStartTimeText)
                .$this->calendar('datum do', "end_$paperContentId", 'Klikněte pro výběr', $eventEndTimeText)

            )
        );
    }

    private function calendar($title, $name, $placeholder, $value) {
        return Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.halfRow')],
                Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'p')], $title)
                .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.calendar')],  //pro výběr kalendáře od-do přidat sem ID, pak změnit loaderOnload.js podle semantic ui  // použito pro $('.ui.calendar').calendar() -kalendář semanticu
                    Html::tag('div',['class'=>$this->classMap->getClass('ContentButtons', 'div.input')],
                        Html::tagNopair('input', ['type'=>'text', 'name'=>$name, 'placeholder'=>$placeholder, 'value'=>$value])
                    )
                )
            );
    }

    private function getTrashButtons(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return
            Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.wrapTrash')],
                Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('TrashButtons', 'button'),
                        'data-tooltip'=>'Obnovit',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/restore",
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
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/delete",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('TrashButtons', 'button.delete')])
                    )
                )
            );

    }
}
