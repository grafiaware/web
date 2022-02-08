<?php
namespace  Component\Renderer\Html\Menu;

use Pes\Text\Html;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuItemInterface;

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
class ItemBlockRendererEditable extends ItemRendererEditable {

    /**
     * Přetěžuje metodu ItemRender pro editable variantu renderování.
     *
     * @return string
     */
    protected function renderEditableItem(MenuItemInterface $menuItem) {
        $menuNode = $this->viewModel->getHierarchyAggregate();
        $menuItem = $menuNode->getMenuItem();
        $active = $menuItem->getActive();

        $innerHtml =
            Html::tag('a',
                [
                'class'=>[
                    $this->classMap->get('Item', 'li a'),   // class - editable v kontejneru
                    $this->classMap->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li')
                    ],
                'href'=>"web/v1/page/item/{$menuItem->getUidFk()}",
                'tabindex'=>0,
                'data-original-title'=>$menuItem->getTitle(),
                'data-uid'=>$menuItem->getUidFk(),
                ],
                $menuItem->getTitle()
                .Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
                    Html::tag('i', ['class'=> $this->classMap->resolve($active, 'Item', 'semafor.published', 'semafor.notpublished')])
                )
            )
            .Html::tag('i', ['class'=>$this->classMap->resolve($this->viewModel->getInnerHtml(), 'Item', 'li.isnotleaf icon', '')])
            .(($this->viewModel->isPresented() AND $this->viewModel->isMenuEditableByUser()) ? $this->renderButtons($menuItem) : '')
            .$this->viewModel->getInnerHtml();
        $html = Html::tag('li',
                ['class'=>[
                    $this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    ]
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(MenuItemInterface $menuItem) {

        return
        Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttons')],
            Html::tag('button',
                ['class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Aktivní/neaktivní položka',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'toggle',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuItem->getUidFk()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($menuItem->getActive(), 'Icons', 'icon.notpublish', 'icon.publish')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'add',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Buttons', 'button.addsiblings')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'delete',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Buttons', 'button.movetotrash')])
            )
        );
    }
}