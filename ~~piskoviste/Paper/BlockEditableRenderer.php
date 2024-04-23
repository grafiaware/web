<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;

use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
use Red\Component\ViewModel\Content\Authored\Paper\NamedPaperViewModelInterface;
use Pes\Text\Html;

use Model\Entity\MenuItemAggregateHierarchyInterface;
use Model\Entity\MenuItemAggregatePaperInterface;

/**
 * Description of BlockEditableRenderer
 *
 * @author pes2704
 */
class BlockEditableRenderer extends HtmlRendererAbstract {

    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $menuNode = $viewModel->getHierarchyNode();
        $paper = $viewModel->getMenuItemPaperAggregate();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponentAggregate()->getName();
        } else {
            $name = "presented";
        }

        if (isset($menuNode) AND isset($paper)) {
            $innerHtml = 
                    $this->renderButtons($menuNode, $paper)
                    .$paper->getPaperSection();
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', ['data-component'=>$name], 'No data item or article for rendering.');
            $style = "display: none;";
        }
        return Html::tag('form', ['method'=>'POST', 'action'=>""],
                   Html::tag('block', ['data-component'=>$name, 'class'=>$this->classMap->get('Component', 'block'), 'style'=>$style], $innerHtml)
               );
    }
    
    private function renderButtons(MenuItemAggregateHierarchyInterface $menuNode, MenuItemAggregatePaperInterface $paper) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
            return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div')],
                Html::tag('button',
                            ['class'=>$this->classMap->get('Buttons', 'div button'),
                            'data-tooltip'=>'Aktivní/neaktivní stránka',
                            'type'=>'submit',
                            'name'=>'toggle',
                            'formmethod'=>'post',
                            'formaction'=>"red/v1/menu/{$menuNode->getUid()}/toggle",
                            ],
                    Html::tag('i', ['class'=>$this->classMap->resolve($menuNode->getHierarchy()->isActive(), 'Buttons', 'div button5 i.on', 'div button5 i.off')])
                )
                .Html::tag('div',
                            ['class'=>$this->classMap->get('Buttons', 'div div'),
                            'data-tooltip'=>'Změnit od '.$menuNode->getHierarchy()->getShowTime().' do '.$menuNode->getHierarchy()->getHideTime(), 'data-position'=>'bottom right',
                            ],
                    Html::tag('i', ['class'=>$this->classMap->get('Buttons', 'div div i')])
                    .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div div div')],
                        Html::tag('p', ['class'=>$this->classMap->get('Buttons', 'div div div p')], 'Od')
                        .Html::tagNopair('input', ['type'=>'date', 'name'=>''])
                        .Html::tag('button', ['class'=>$this->classMap->get('Buttons', 'div div div button'), 'name'=>''], 'Trvale')
                        .Html::tag('p',['class'=>$this->classMap->get('Buttons', 'div div div p')], 'Do')
                        .Html::tagNopair('input', ['type'=>'date', 'name'=>''])
                        .Html::tag('button', ['class'=>$this->classMap->get('Buttons', 'div div div button'), 'name'=>''], 'Trvale')
                .Html::tag('button',
                                    ['class'=>$this->classMap->get('Buttons', 'div div div button'),
                            'type'=>'submit',
                            'name'=>'time',
                            'formmethod'=>'post',
                            'formaction'=>"red/v1/menu/{$menuNode->getUid()}/time",
                                    ], 'Uložit'
                )

                    )
                )
            );
    }
}
