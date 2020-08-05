<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;
use Model\Entity\HierarchyNodeInterface;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

use Pes\Utils\Directory;

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
class ItemTrashRenderer extends HtmlRendererAbstract {

    public function render($data=NULL) {
        return $this->privateRender($data);
    }

    private function privateRender(ItemViewModel $itemViewModel=NULL) {
        $menuNode = $itemViewModel->getMenuNode();
        $innerHtml = Html::tag('i', ['class'=> $this->classMap->getClass('Item', 'li i1')])
                    .Html::tag('a', [
                        'class'=>$this->classMap->getClass('Item', 'li a'),
                        'href'=>"www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}",
                         ],
                        $menuNode->getMenuItem()->getTitle()
                    )
                    .Html::tag('i', ['class'=>$this->classMap->resolveClass($itemViewModel->getInnerHtml(), 'Item', 'li i')])
                    .(($itemViewModel->getIsPresented() AND !$itemViewModel->getReadonly()) ? $this->renderButtons($menuNode) : '')
                    .$itemViewModel->getInnerHtml();
        $html = Html::tag('li',
                ['class'=>[
                    $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', 'li'),
                    $this->classMap->resolveClass($itemViewModel->getIsPresented(), 'Item', 'li.presented', 'li'),
                    ],
                 "style" => $itemViewModel->isRestored() ? "background-color: coral" : ""
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(HierarchyNodeInterface $menuNode) {

        return
        Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div')],
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Trvale odstranit',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'delete',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/delete",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button1 i'),],
                        Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button1 i1'),])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button1 i2'),])
                )
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/restore",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button4 i')])
            )
        );
    }
}
