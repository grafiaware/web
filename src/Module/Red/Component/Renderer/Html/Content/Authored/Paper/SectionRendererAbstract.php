<?php
namespace Red\Component\Renderer\Html\Content\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Entity\PaperSectionInterface;
use Red\Middleware\Redactor\Controler\SectionsControler;

use Pes\Text\Html;

/**
 * Description of SectionRendererAbstract
 *
 * @author pes2704
 */
abstract class SectionRendererAbstract extends HtmlRendererAbstract {

    protected function renderSection(PaperSectionInterface $paperSection) {
        $html =
                Html::tag('section', ['class'=>$this->classMap->get('Content', 'section')],$this->renderContent($paperSection));
        return $html;
    }
    
    protected function renderEditableSectionPreview(PaperSectionInterface $paperSection) {
        return Html::tag('section', ['class'=>$this->classMap->get('Content', 'section')],
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                        $this->renderRibbon($paperSection)
                )
                .$this->renderContent($paperSection)
            );
    }
    
    protected function renderEditableSection(PaperViewModelInterface $viewModel, PaperSectionInterface $paperSection) {
        return 
            Html::tag('section', ['class'=>$this->classMap->get('Content', 'section')],
                Html::tag("form", ['method'=>'POST', "action"=>"javascript:void(0);"],  // potlačí submit po stisku Enter
                    Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                            [
                            $this->renderRibbon($paperSection),
                            $this->renderSectionButtons($paperSection)
                            ]
                    )
                )
                .
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/section/{$paperSection->getId()}"],
                    Html::tag('content',
                        [
                            'id'=> implode("_", [SectionsControler::SECTION_CONTENT, $paperSection->getId(), $viewModel->getComponentUid()]),           // POZOR - id musí být unikátní - jinak selhává tiny selektor
                            'data-red-menuitemid'=>$viewModel->getMenuItemId(),
                            'class'=>$this->classMap->get('Content', 'content.edit-html')
                        ],
                        $paperSection->getContent() ?? ""
                    )
                )
            );
    }

    protected function renderTrashSectionPrewiew(PaperSectionInterface $paperSection) {
        return
        Html::tag('section', ['class'=>$this->classMap->get('Content', 'section.trash')],
            Html::tag("form", ['method'=>'POST', "action"=>"javascript:void(0);"],  // potlačí submit po stisku Enter
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                    Html::tag('i',['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
                )
            )
            .Html::tag('div',
                [
                    'id' => "content_{$paperSection->getId()}",  // id nemusí být na stránce unikátní není proměnná formu
                    'class'=>$this->classMap->get('Content', 'div.trash_content')
                ],
                $paperSection->getContent() ?? ""
            )
        );
    }
    
    protected function renderTrashSection(PaperSectionInterface $paperSection) {
        return
        Html::tag('section', ['class'=>$this->classMap->get('Content', 'section.trash')],
            Html::tag("form", ['method'=>'POST', "action"=>"javascript:void(0);"],  // potlačí submit po stisku Enter
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                    $this->renderTrashButtons($paperSection)
                    .Html::tag('i',['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
                )
            )
//            .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')],
//                    Html::tag('i',['class'=>$this->classMap->get('Content', 'i.trash')])
//            )
            .Html::tag('div',
                [
                    'id' => "content_{$paperSection->getId()}",  // id nemusí být na stránce unikátní není proměnná formu
                    'class'=>$this->classMap->get('Content', 'div.trash_content')
                ],
                $paperSection->getContent() ?? ""
            )
        );
    }
    
    private function renderContent(PaperSectionInterface $paperSection) {
        $html =
            Html::tag('content', [
                        'id' => "content_{$paperSection->getId()}",
                        'class'=>$this->classMap->get('Content', 'content'),
                        'data-owner'=>$paperSection->getEditor()
                    ],
                $paperSection->getContent()
            );
        return $html;
    }
    
    private function renderRibbon(PaperSectionInterface $paperContent) {
        $priority = $paperContent->getPriority();
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();

        $styleLine ="fill:none; stroke:#ae00ff; stroke-width:2";
        $styleRectShow =  $actual ? "fill:purple; stroke:#333333; stroke-width:2" : "fill:lightgrey; stroke:#222222; stroke-width:1";
        $styleRectEvent =  ($paperContent->getEventStartTime() OR $paperContent->getEventEndTime())
                ? "fill:gold; stroke:#333333; stroke-width:1"
                : "fill:#cccccc; fill-opacity:0.5; stroke:#333333; stroke-width:1";
        $styleCircle = $active ? "fill:#21ba45; stroke:#000000; stroke-width:0" : "fill:#ffffff; stroke:#db2828; stroke-width:2";
        $clockStroke = $actual ? "lime" : "red";

        $sLeft = $this->left($paperContent->getShowTime());
        $sRight = $this->right($paperContent->getHideTime());
        $eLeft = $this->left($paperContent->getEventStartTime());
        $eRight = $this->right($paperContent->getEventEndTime());

        $svgWidth=100;

        $showLeft = sprintf('%d%%', $sLeft);
        $showWidth = sprintf('%d%%', max($sRight-$sLeft, 4));
        $eventLeft = sprintf('%d%%', $eLeft);
        $eventWidth = sprintf('%d%%', max($eRight-$eLeft, 4));
        $nowPoint = sprintf('%d%%', $svgWidth/2);

        return

        Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor-radek')], //aktivní/neaktivní content
            Html::tag('div',
               [
               'class'=> 'ikona-popis',
               'data-tooltip'=> $active ? "published" : "not published",
               'data-position'=>'bottom center',
               ],
                Html::tag('i',
                   [
                   'class'=> $this->classMap->resolve($active, 'Icons','semafor.published', 'semafor.notpublished'),
                   ]
                )
            )
            .Html::tag('div',
               [
               'class'=> 'ikona-popis',
               'data-tooltip'=> $actual ? "actual" : "not actual",
               'data-position'=>'bottom center',
               ],
                Html::tag('i',
                   [
                   'class'=> $this->classMap->resolve($actual, 'Icons','semafor.actual', 'semafor.notactual'),
                   ]
                )
            )
        )
    //SVG
//        Html::tag('svg', ["width"=>"25", "height"=>"30", 'class'=>$this->classMap->get('Content', 'ribbon.svg')],
//            Html::tag('circle', ["cx"=>"50%", "cy"=>"50%", "r"=>"8", "style"=>$styleCircle])
//            .Html::tag('title', [], 'Něco napsáno')
//        )
//        .Html::tag('svg', ["width"=>"35", "height"=>"30", 'class'=>$this->classMap->get('Content', 'ribbon.svg')],
//               Html::tag('path', ["fill-rule"=>"nonzero", "clip-rule"=>"evenodd", "fill"=>"white", "stroke"=>$clockStroke, "d"=>"M15 1c5.623 0 10 3.377 10 10s-3.377 10-10 10-10-3.377-10-10 3.377-10 10-10zm0 1c5.623 0 9 2.929 9 9s-2.929 9-9 9-9-2.929-9-9 2.929-9 9-9zm0 9h5v1h-6v-7h1v6z"]),
//               //Html::tag('path', ["fill-rule"=>"nonzero", "clip-rule"=>"evenodd", "fill"=>"white", "stroke"=>$clockStroke, "d"=>"M15 0c6.623 0 12 5.377 12 12s-5.377 12-12 12-12-5.377-12-12 5.377-12 12-12zm0 1c6.071 0 11 4.929 11 11s-4.929 11-11 11-11-4.929-11-11 4.929-11 11-11zm0 11h6v1h-7v-9h1v8z"]),
//        )
        .Html::tag('svg', ["width"=>$svgWidth, "height"=>"30", 'class'=>$this->classMap->get('Content', 'ribbon.svg')],
           [
                Html::tag('line', ["x1"=>"0", "y1"=>"50%", "x2"=>"100%", "y2"=>"50%", "style"=>$styleLine]),
                Html::tag('line', ["x1"=>$nowPoint, "y1"=>"10%", "x2"=>$nowPoint, "y2"=>"90%", "style"=>$styleLine]),
                Html::tag('rect', ["x"=>$showLeft, "y"=>6, "rx"=>4, "ry"=>4, "width"=>$showWidth, "height"=>"50%", "style"=>$styleRectShow]),
                Html::tag('rect', ["x"=>$eventLeft, "y"=>10, "rx"=>4, "ry"=>4, "width"=>$eventWidth, "height"=>"60%", "style"=>$styleRectEvent]),

            ]
        )
        .Html::tag('div', ['class'=>$this->classMap->get('Content', 'ribbon.priority'), 'data-tooltip'=>'priority', 'data-position'=>'bottom center',],
//            Html::tag('i', [ 'class'=>$this->classMap->get('Icons', 'icon.clipboard')],
                Html::tag('span', [ 'class'=>''],
                    $priority
                )
//            )
        );
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
//            $left = $this->krivka($l)*53+47;
            $left = $this->krivka($l)*50+50;
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
//            $right = $this->krivka($r)*47+53;
            $right = $this->krivka($r)*50+50;
        } else {
            $right = 110;
        }
        return $right;
    }

    private function getShowTimeText(PaperSectionInterface $paperContent) {
        $showTime = $paperContent->getShowTime();
        return isset($showTime) ? $showTime->format("d.m.Y") : '';
    }

    private function getHideTimeText(PaperSectionInterface $paperContent) {
        $hideTime = $paperContent->getHideTime();
        return isset($hideTime) ? $hideTime->format("d.m.Y") : '';
    }

    private function getEventStartTimeText(PaperSectionInterface $paperContent) {
        $eventStartTime = $paperContent->getEventStartTime();
        return isset($eventStartTime) ? $eventStartTime->format("d.m.Y") : '';
    }

    private function getEventEndTimeText(PaperSectionInterface $paperContent) {
        $eventEndTime = $paperContent->getEventEndTime();
        return isset($eventEndTime) ? $eventEndTime->format("d.m.Y") : '';
    }

    private function textDatumyZobrazeni(PaperSectionInterface $paperContent) {
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

    private function textDatumyUdalosti(PaperSectionInterface $paperContent) {
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
            $textDatumyUdalosti = "Událost nemá trvání.";
        }
        return $textDatumyUdalosti;
    }

    private function renderSectionButtons(PaperSectionInterface $paperSection) {

        $sectionId = $paperSection->getId();
        $active = $paperSection->getActive();
        $actual = $paperSection->getActual();

        $btnAktivni = Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Aktivní/neaktivní obsah',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
                );
        $btnDoKose = Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Do koše',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/trash",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
                );
        $btnDatumyZobrazeni = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button.showDate'),
                    'data-tooltip'=> $this->textDatumyZobrazeni($paperSection),
                    'data-position'=>'bottom center',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.changedisplaydate')])
                );
            ## buttony datumy zobrazeni
            $btnZobrazeniTrvale = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.permanently')])
                );
            $btnZobrazeniUlozit = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/actual",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.save')])
                );
            $btnZobrazeniZrusitUpravy = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button.content'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'bottom center',
                    'onclick'=>"event.preventDefault(); this.form.reset();"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cancel')])
            );
        $btnDatumyUdalosti = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button.eventDate'),
                    'data-tooltip'=> $this->textDatumyUdalosti($paperSection),
                    'data-position'=>'bottom center',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.changeeventdate')])
                );
            ## buttony datumy udalost
            $btnUdalostTrvale = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'permanent',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/event",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.permanently')])
                );
            $btnUdalostUlozit = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'calendar',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/event",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.save')])
                );
            $btnUdalostZrusitUpravy = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button.content'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'bottom center',
                    'onclick'=>"event.preventDefault(); this.form.reset();"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cancel')])
                );
        $btnOJednuVys = Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Posunout o jednu výš',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/up",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowup')])
                    )
                );
        $btnOJednuNiz = Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Posunout o jednu níž',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/down",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowdown')])
                    )
                );
        $btnPridatObsahPred = Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Přidat další sekci před',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/add_above",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowup')])
                    )
                );
        $btnPridatObsahZa = Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Přidat další sekci za',
                    'data-position'=>'bottom center',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/section/$sectionId/add_below",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowdown')])
                    )
                );


        return
        Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapContent')],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    $btnAktivni.$btnDoKose
                    )
            .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    $btnDatumyZobrazeni.$btnDatumyUdalosti
                    )
            .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    $btnOJednuVys.$btnOJednuNiz
                    )
            .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    $btnPridatObsahPred.$btnPridatObsahZa
            )
        )
        .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapShowDate')],
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsEditDate')],
                $btnZobrazeniTrvale.$btnZobrazeniUlozit.$btnZobrazeniZrusitUpravy
             )
            .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.grid')],

                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.wholeRow')],
                    Html::tag('p', ['class'=>''], 'Uveřejnit obsah')
                )
                .$this->renderCalendar('datum od', "show_$sectionId", 'Klikněte pro výběr', $this->getShowTimeText($paperSection))
                .$this->renderCalendar('datum do', "hide_$sectionId", 'Klikněte pro výběr', $this->getHideTimeText($paperSection))

            )
        )
        .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapEventDate')],       // display none <-> display flex
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsEditDate')],
                $btnUdalostTrvale.$btnUdalostUlozit.$btnUdalostZrusitUpravy
            )
            .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.grid')],

                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.wholeRow')],
                    Html::tag('p', ['class'=>''], 'Nastavit datum události')
                )
                .$this->renderCalendar('datum od', "start_$sectionId", 'Klikněte pro výběr', $this->getEventStartTimeText($paperSection))
                .$this->renderCalendar('datum do', "end_$sectionId", 'Klikněte pro výběr', $this->getEventEndTimeText($paperSection))

            )
        );
    }

    private function renderCalendar($title, $name, $placeholder, $value) {
        return Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.halfRow')],
                Html::tag('p', ['class'=>''], $title)
                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.calendar')],  //pro výběr kalendáře od-do přidat sem ID, pak změnit loaderOnload.js podle semantic ui  // použito pro $('.ui.calendar').calendar() -kalendář semanticu
                    Html::tag('div',['class'=>$this->classMap->get('Content', 'div.input')],
                        Html::tagNopair('input', ['type'=>'text', 'name'=>$name, 'placeholder'=>$placeholder, 'value'=>$value])
                    )
                )
            );
    }

    private function renderTrashButtons(PaperSectionInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.wrapTrash')],
                Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Obnovit',
                        'data-position'=>'bottom center',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/section/$paperContentId/restore",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.restore')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('Buttons', 'button'),
                        'data-tooltip'=>'Smazat',
                        'data-position'=>'bottom center',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/section/$paperContentId/delete",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.delete')])
                    )
                )
            );

    }
    
}
