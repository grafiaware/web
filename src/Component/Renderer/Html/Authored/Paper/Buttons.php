<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Pes\View\Renderer\ClassMap\ClassMapInterface;

use Model\Entity\PaperAggregatePaperContentInterface;
use Model\Entity\PaperInterface;
use Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of AuthoredEditableRenderer
 *
 * @author pes2704
 */
class Buttons {

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

    public function getPaperTemplateButtonsForm(PaperInterface $paper) {
        $paperId = $paper->getId();
        $paperTemplatesName = $paper->getTemplate();
        $selectHtml = '';
//        foreach ($paperTemplatesName as $type) {
//            $selectHtml .=  Html::tag('div', ['value'=>$type->getType(), 'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.item')],
//                                $type->getType()
//                            );
//
//        }
        $postName = 'folder_'.$paperId;
        $postItems = [
            'Course'=>'course',
            'Contact'=>'contact',
            'Výchozí'=>'default'
        ];
        $items = [];
        $class = $this->classMap->getClass('PaperTemplateSelect', 'div.item');
        foreach ($postItems as $title => $value) {
            $items[] = Html::tag('div', ['class'=>$class, 'value'=>$value], $title);
        }
        return
            Html::tag('form', ['method'=>'POST', 'action'=>"api/v1/paper/$paperId/template"],
                Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateButtons', 'div.paperTemplate'), 'data-tooltip'=>'Výběr šablony stránky'],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperTemplateButtons', 'button.templateSelect')])
                    .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.menu')],
                        Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.header')], 'Vyberte šablonu stránky')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.selection')],
                            Html::tag('input', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'input'), 'type'=>'hidden', 'name'=>$postName, 'onchange'=>'this.form.submit()'] )
                            .Html::tag('i', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'i.dropdown')])
                            .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.text')], 'Šablona')
                            .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.scrollmenu')],
                                    implode(PHP_EOL, $items)
                            )
                        )
                    )
                )
            );
    }

    public function getPaperButtonsForm(PaperInterface $paper) {
        $paperId = $paper->getId();

        $buttons = [];
        if ($paper instanceof PaperAggregateInterface AND $paper->getPaperContentsArray()) {
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

}
