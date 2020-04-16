<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Component\ViewModel\Authored\Paper\NamedPaperViewModelInterface;
use Pes\Text\Html;

use Model\Entity\MenuNodeInterface;
use Model\Entity\PaperInterface;

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
        $menuNode = $viewModel->getMenuNode();
        $paper = $viewModel->getPaper();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponent()->getName();
        } else {
            $name = "presented";
        }

        if (isset($menuNode) AND isset($paper)) {
            $buttons = Html::tag('form', ['method'=>'POST', 'action'=>""], 
                            $this->renderButtons($menuNode, $paper)
                        );
            $innerHtml =
                    $buttons 
                    .Html::tag('form', ['method'=>'POST', 'action'=>""],
                        Html::tag('block', ['class'=>$this->classMap->getClass('Component', 'div block')],
                            $paper->getContent()
                        )
                    );
            $style = "display: block;";
        } else {
            $innerHtml = Html::tag('div', ['data-component'=>$name], 'No data item or article for rendering.');
            $style = "display: none;";
        }
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->getClass('Component', 'div'), 'style'=>$style], $innerHtml);

    }

    private function renderButtons(MenuNodeInterface $menuNode, PaperInterface $paper) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
            return
            Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.page')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Aktivní/neaktivní stránka',
                    'data-position'=>'bottom right',
                    'type'=>'submit',
                    'name'=>'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->resolveClass($menuNode->getMenuItem()->getActive(), 'Buttons', 'div button5 i.on', 'div button5 i.off')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button.date'),
                    'data-tooltip'=>'Změnit od '.$menuNode->getMenuItem()->getShowTime().' do '.$menuNode->getMenuItem()->getHideTime(), 
                    'data-position'=>'bottom right',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div div i')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.date')],
                Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Trvale',
                    'data-position'=>'bottom right',
                    'type'=>'submit',
                    //'name'=>'',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button6 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Uložit',
                    'data-position'=>'bottom right',
                    'type'=>'submit',
                    //'name'=>'',
                    'formmethod'=>'post',
                    'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button7 i')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button.page'),
                    'data-tooltip'=>'Zrušit úpravy',
                    'data-position'=>'bottom right',
                    //'type'=>'submit',
                    //'name'=>'',
                    //'formmethod'=>'post',
                    //'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    'onclick'=> "this.form.reset()"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button8 i')])
                )
                .Html::tag('div', [
                    'class'=>$this->classMap->getClass('Buttons', 'div button'),
                    'data-tooltip'=>'Změnit od '.$menuNode->getMenuItem()->getShowTime().' do '.$menuNode->getMenuItem()->getHideTime(), 
                    'data-position'=>'bottom right',
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div div i')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.date2')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div'), ],
                        Html::tag('p', ['class'=>$this->classMap->getClass('Buttons', 'div div div p')], 'Uveřejnit od')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div div')],
                            Html::tag('div',['class'=>$this->classMap->getClass('Buttons', 'div div div div div')],
                                Html::tagNopair('input', ['type'=>'text', 'name'=>'kalendarOD', 'placeholder'=>'Klikněte pro výběr data'])
                            )
                         )
                        .Html::tag('p', ['class'=>$this->classMap->getClass('Buttons', 'div div div p')], 'Uveřejnit do')
                        .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div div div div')],
                            Html::tag('div',['class'=>$this->classMap->getClass('Buttons', 'div div div div div')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>'kalendarDO', 'placeholder'=>'Klikněte pro výběr data'])
                        )
                    )
                )
            );
    }
}
