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

use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
abstract class AuthoredEditableRenderer extends HtmlRendererAbstract{
    protected function renderButtons(MenuNodeInterface $menuNode, PaperInterface $paper) {
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
}
