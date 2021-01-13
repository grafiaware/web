<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

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
class ItemRenderer extends HtmlRendererAbstract {

    public function render($data=NULL) {
        return $this->privateRender($data);
    }

    private function privateRender(ItemViewModel $itemViewModel=NULL) {
        $menuNode = $itemViewModel->getMenuNode();
        $innerHtml = Html::tag('a', ['class'=>$this->classMap->getClass('Item', 'li a'), 'href'=> "www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}" ],
                        Html::tag('span', ['class'=>$this->classMap->getClass('Item', 'li a span')],
                            $menuNode->getMenuItem()->getTitle()
                            .Html::tag('i', ['class'=>$this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                        )
                    )
                    .$itemViewModel->getInnerHtml();
        $html = Html::tag('li',
                ['class'=>[
                    $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', ($itemViewModel->getRealDepth() >= 3) ? 'li.item' : 'li.dropdown'),
                    $this->classMap->resolveClass($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    ]
                ],
                $innerHtml);
        return $html;
    }
}
