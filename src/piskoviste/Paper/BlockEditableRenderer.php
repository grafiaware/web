<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;
use Pes\Text\Html;

use Model\Entity\HierarchyAggregateInterface;
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
                    .$paper->getPaperContent();
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', ['data-component'=>$name], 'No data item or article for rendering.');
            $style = "display: none;";
        }
        return Html::tag('form', ['method'=>'POST', 'action'=>""],
                   Html::tag('block', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Component', 'block'), 'style'=>$style], $innerHtml)
               );
    }
    
    private function renderButtons(HierarchyAggregateInterface $menuNode, MenuItemAggregatePaperInterface $paper) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
            return
            Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div')],
                Html::tag('button',
                            ['class'=>$this->classMap->getClass('Buttons', 'div button'),
                            'data-tooltip'=>'Aktivní/neaktivní stránka',
                            'type'=>'submit',
                            'name'=>'toggle',
                            'formmethod'=>'post',
                            'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
                            ],
                    Html::tag('i', ['class'=>$this->classMap->resolveClass($menuNode->getMenuItem()->getActive(), 'Buttons', 'div button5 i.on', 'div button5 i.off')])
                )
                .Html::tag('div',
                            ['class'=>$this->classMap->getClass('Buttons', 'div div'),
                            'data-tooltip'=>'Změnit od '.$menuNode->getMenuItem()->getShowTime().' do '.$menuNode->getMenuItem()->getHideTime(), 'data-position'=>'bottom right',
                            ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div div i')])
                    .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div')],
                        Html::tag('p', ['class'=>$this->classMap->getClass('Buttons', 'div div div p')], 'Od')
                        .Html::tagNopair('input', ['type'=>'date', 'name'=>''])
                        .Html::tag('button', ['class'=>$this->classMap->getClass('Buttons', 'div div div button'), 'name'=>''], 'Trvale')
                        .Html::tag('p',['class'=>$this->classMap->getClass('Buttons', 'div div div p')], 'Do')
                        .Html::tagNopair('input', ['type'=>'date', 'name'=>''])
                        .Html::tag('button', ['class'=>$this->classMap->getClass('Buttons', 'div div div button'), 'name'=>''], 'Trvale')
                .Html::tag('button',
                                    ['class'=>$this->classMap->getClass('Buttons', 'div div div button'),
                            'type'=>'submit',
                            'name'=>'time',
                            'formmethod'=>'post',
                            'formaction'=>"api/v1/menu/{$menuNode->getUid()}/time",
                                    ], 'Uložit'
                )

                    )
                )
            );
    }
}
