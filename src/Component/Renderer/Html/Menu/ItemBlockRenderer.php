<?php
namespace  Component\Renderer\Html\Menu;

use Pes\Text\Html;
use Red\Model\Entity\HierarchyAggregateInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Svisle
 *
 * @author pes2704
 */
class ItemBlockRenderer extends ItemRenderer {

    /**
     * Přetěžuje metodu ItemRender pro editable variantu renderování.
     *
     * @return string
     */
    protected function renderEditableItem() {
        $menuNode = $this->viewModel->getMenuNode();
        $menuItem = $menuNode->getMenuItem();
        $active = $menuItem->getActive();

        $innerHtml =
            Html::tag('a',
                [
                'class'=>[
                    $this->classMapEditable->get('Item', 'li a'),   // class - editable v kontejneru
                    $this->classMapEditable->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li')
                    ],
                'href'=>"web/v1/page/item/{$menuNode->getUid()}",
                'tabindex'=>0,
                'data-original-title'=>$menuItem->getTitle(),
                'data-uid'=>$menuNode->getUid(),
                ],
                $menuItem->getTitle()
                .Html::tag('span', ['class'=>$this->classMapEditable->get('Item', 'semafor')],
                    Html::tag('i', ['class'=> $this->classMapEditable->resolve($active, 'Item', 'semafor.published', 'semafor.notpublished')])
                )
            )
            .Html::tag('i', ['class'=>$this->classMapEditable->resolve($this->viewModel->getInnerHtml(), 'Item', 'li.isnotleaf icon', '')])
            .(($this->viewModel->isPresented() AND $this->viewModel->isMenuEditableByUser()) ? $this->renderButtons($menuNode) : '')
            .$this->viewModel->getInnerHtml();
        $html = Html::tag('li',
                ['class'=>[
                    $this->classMapEditable->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    ]
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(HierarchyAggregateInterface $menuNode) {

        return
        Html::tag('div', ['class'=>$this->classMapEditable->get('CommonButtons', 'div.buttons')],
            Html::tag('button',
                ['class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Aktivní/neaktivní položka',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'toggle',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuNode->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMapEditable->resolve($menuNode->getMenuItem()->getActive(), 'CommonButtons', 'button.notpublish', 'button.publish')])
            )
            .Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'add',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.addsiblings')])
            )
            .Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'delete',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.movetotrash')])
            )
        );
    }
}