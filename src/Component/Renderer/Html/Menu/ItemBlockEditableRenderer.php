<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlModelRendererAbstract;
use Pes\Text\Html;
use Model\Entity\HierarchyAggregateInterface;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

use Pes\View\Renderer\RendererModelAwareInterface;

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
class ItemBlockEditableRenderer extends HtmlModelRendererAbstract implements RendererModelAwareInterface {

    public function render($data=NULL) {
        /** @var ItemViewModel $itemViewModel */
        $itemViewModel = $this->viewModel;
        $menuNode = $itemViewModel->getMenuNode();
        $menuItem = $menuNode->getMenuItem();
        $active = $menuItem->getActive();

        $innerHtml =
            Html::tag('a',
                [
                'class'=>$this->classMap->getClass('Item', 'li a'),   // class - editable v kontejneru
                'href'=>"www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}",
                'tabindex'=>0,
                'data-original-title'=>$menuItem->getTitle(),
                'data-uid'=>$menuNode->getUid(),
                ],
                $menuItem->getTitle()
                .Html::tag('span', ['class'=>$this->classMap->getClass('Item', 'semafor')],
                    Html::tag('i', ['class'=> $this->classMap->resolveClass($active, 'Item', 'semafor.published', 'semafor.notpublished')])
                )
            )
            .Html::tag('i', ['class'=>$this->classMap->resolveClass($itemViewModel->getInnerHtml(), 'Item', 'li.isnotleaf icon')])
            .(($itemViewModel->isPresented() AND !$itemViewModel->isReadonly()) ? $this->renderButtons($menuNode) : '')
            .$itemViewModel->getInnerHtml();
        $html = Html::tag('li',
                ['class'=>[
                    $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', ($itemViewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolveClass($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    ]
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(HierarchyAggregateInterface $menuNode) {

        return
        Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.buttons')],
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'delete',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.movetotrash'),])
            )
            .Html::tag('button',
                ['class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Aktivní/neaktivní položka',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'toggle',
                'formmethod'=>'post',
                'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolveClass($menuNode->getMenuItem()->getActive(), 'Buttons', 'button.notpublish', 'button.publish')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'add',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.addsiblings')])
            )
        );
    }
}