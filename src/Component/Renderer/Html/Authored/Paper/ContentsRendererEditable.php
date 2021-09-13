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
            $sections[] = 'No paper.';
        }
        return $sections;
    }

    /**
     * Vrací hodnoty od -1 do 1
     * @param float $x
     * @return float
     */
    private function krivka($x) {
        $k = (($x-1)>=0 ? 1 : -1)*pow(abs($x-1), (1/5));
        return $k;
    }

    /**
     * Vrací hodnoty od 0 do 50 nebo -10. Pro čas v daleké minulosti vrací 0, pro čas "now" vrací 50. Pro null vrací -10.
     * @param \DateTimeInterface $leftTime
     * @return type
     */
    private function left(\DateTimeInterface $leftTime = null) {
        if (isset($leftTime)) {
            $l = $leftTime->getTimestamp()/(new \DateTime("now"))->getTimestamp();
            $left = $this->krivka($l)*53+47;
        } else {
            $left = -10;
        }
        return $left;
    }

    /**
     * Vrací hodnoty od 50 do 100 nebo 110. Pro čas "now" vrací 50, pro čas v daleké budoucnosti vrací 100. Pro null vrací 110.
     * @param \DateTimeInterface $rightTime
     * @return int
     */
    private function right(\DateTimeInterface $rightTime = null) {
        if (isset($rightTime)) {
            $r = $rightTime->getTimestamp()/(new \DateTime("now"))->getTimestamp();
            $right = $this->krivka($r)*47+53;
        } else {
            $right = 110;
        }
        return $right;
    }

    private function getContent(PaperContentInterface $paperContent) {
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();
        $now = new \DateTime("now");
        $future = $paperContent->getShowTime() > $now;
        $past = $paperContent->getHideTime() < $now;  // pro zobrszeno trvale - null je vždy menší a $passed je true - vyhodnucuji nejprve $actual, nevadí to

//                    '<svg width="100" height="30" style="position: relative; top: -15px">
//                        <line x1="0" y1="50%" x2="100%" y2="50%" fill="none" stroke="#aeaeae" stroke-width="5"/>
//                        <rect x="20%" y="4" width="70%" height="75%" fill="#6435c9c2" stroke="#000000" stroke-width="1"/>
//                        <rect x="35%" y="8" width="50%" height="50%" fill="#ffe21fc4" stroke="#000000" stroke-width="1"/>
//                        <circle cx="47" cy="50%" r="5" fill="'.$circleColor.'" stroke="#000000" stroke-width="0"/>
//                     </svg>'

        $styleLine ="fill:none; stroke:#aeaeae; stroke-width:5";
        $styleRectShow =  $actual ? "fill:purple; stroke:#333333; stroke-width:2" : "fill:grey; stroke:#222222; stroke-width:2";
        $styleRectEvent =  "fill:gold; stroke:#333333; stroke-width:1";
        $styleCircle = $active ? "fill:#21ba45; stroke:#000000; stroke-width:0" : "fill:#db2828; stroke:#000000; stroke-width:0";

        $sLeft = $this->left($paperContent->getShowTime());
        $sRight = $this->right($paperContent->getHideTime());
        $eLeft = $this->left($paperContent->getEventStartTime());
        $eRight = $this->right($paperContent->getEventEndTime());

        $point = 50;

        $showLeft = sprintf('%d%%', $sLeft);
        $showWidth = sprintf('%d%%', $sRight-$sLeft);
        $eventLeft = sprintf('%d%%', $eLeft);
        $eventWidth = sprintf('%d%%', $eRight-$eLeft);
        $circlePosition = sprintf('%d%%', $point);

        $html =
            Html::tag('section', ['class'=>$this->classMap->getClass('Content', 'section')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.ribbon')],
                    Html::tag('svg', ["width"=>"100", "height"=>"30", "style"=>"position: relative; top: -15px"],
                           [
                                Html::tag('line', ["x1"=>"0", "y1"=>"50%", "x2"=>"100%", "y2"=>"50%", "style"=>$styleLine]),
                                Html::tag('rect', ["x"=>$showLeft, "y"=>4, "rx"=>0, "ry"=>0, "width"=>$showWidth, "height"=>"60%", "style"=>$styleRectShow]),
                                Html::tag('rect', ["x"=>$eventLeft, "y"=>8, "rx"=>4, "ry"=>4, "width"=>$eventWidth, "height"=>"60%", "style"=>$styleRectEvent]),
                                Html::tag('circle', ["cx"=>$circlePosition, "cy"=>"50%", "r"=>"5", "style"=>$styleCircle]),
                            ]
                        )
                        .
                        $this->getContentButtons($paperContent)

                    )
//                .Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')],
//                    Html::tag('div',
//                       [
//                       'class'=> 'ikona-popis',
//                       'data-tooltip'=> $active ? "published" : "not published",
//                       ],
//                        Html::tag('i',
//                           [
//                           'class'=> $this->classMap->resolveClass($active, 'Content','i1.published', 'i1.notpublished'),
//                           ]
//                        )
//                    )
//                        'i2.published' => 'calendar check icon green',
//                        'i2.notactive' => 'calendar plus icon yellow',
//                        'i2.notactual' => 'calendar minus icon orange',
//                        'i2.notactivenotactual' => 'calendar times icon red',
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
//                )
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
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.ribbon')],
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

    private function getShowTimeText(PaperContentInterface $paperContent) {
        $showTime = $paperContent->getShowTime();
        return isset($showTime) ? $showTime->format("d.m.Y") : '';
    }

    private function getHideTimeText(PaperContentInterface $paperContent) {
        $hideTime = $paperContent->getHideTime();
        return isset($hideTime) ? $hideTime->format("d.m.Y") : '';
    }

    private function getEventStartTimeText(PaperContentInterface $paperContent) {
        $eventStartTime = $paperContent->getEventStartTime();
        return isset($eventStartTime) ? $eventStartTime->format("d.m.Y") : '';
    }

    private function getEventEndTimeText(PaperContentInterface $paperContent) {
        $eventEndTime = $paperContent->getEventEndTime();
        return isset($eventEndTime) ? $eventEndTime->format("d.m.Y") : '';
    }

    private function textDatumyZobrazeni(PaperContentInterface $paperContent) {
        $showTimeText = $this->getShowTimeText($paperContent);
        $hideTimeText = $this->getHideTimeText($paperContent);
        if ($showTimeText) {
            if ($hideTimeText) {
                $textDatumyZobrazeni = "Zobrazeno od $showTimeText  do $hideTimeText";
            } else {
                $textDatumyZobrazeni = "Zobrazeno od $showTimeText";
            }
        } elseif ($hideTimeText) {
            $textDatumyZobrazeni = "Zobrazeno do $hideTimeText";
        } else {
            $textDatumyZobrazeni = "Zobrazeno trvale";
        }
        return $textDatumyZobrazeni;
    }

    private function textDatumyUdalosti(PaperContentInterface $paperContent) {
        $eventStartTimeText = $this->getEventStartTimeText($paperContent);
        $eventEndTimeText = $this->getEventEndTimeText($paperContent);
        if ($eventStartTimeText) {
            if ($eventEndTimeText) {
                $textDatumyUdalosti = "Událost se koná od $eventStartTimeText  do $eventEndTimeText";
            } else {
                $textDatumyUdalosti = "Událost se koná od $eventStartTimeText";
            }
        } elseif ($eventEndTimeText) {
            $textDatumyUdalosti = "Událost se koná do $eventEndTimeText";
        } else {
            $textDatumyUdalosti = "Událost se koná trvale";
        }
        return $textDatumyUdalosti;
    }

    private function getContentButtons(PaperContentInterface $paperContent) {

        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();

        $btnAktivni = Html::tag('button',
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
                    'data-tooltip'=> $this->textDatumyZobrazeni($paperContent),
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
                    'data-tooltip'=> $this->textDatumyUdalosti($paperContent),
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
                .$this->calendar('datum od', "show_$paperContentId", 'Klikněte pro výběr', $this->getShowTimeText($paperContent))
                .$this->calendar('datum do', "hide_$paperContentId", 'Klikněte pro výběr', $this->getHideTimeText($paperContent))

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
                .$this->calendar('datum od', "start_$paperContentId", 'Klikněte pro výběr', $this->getEventStartTimeText($paperContent))
                .$this->calendar('datum do', "end_$paperContentId", 'Klikněte pro výběr', $this->getEventEndTimeText($paperContent))

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
