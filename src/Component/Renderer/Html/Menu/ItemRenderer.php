<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlModelRendererAbstract;
use Pes\Text\Html;
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
class ItemRenderer extends HtmlModelRendererAbstract implements RendererModelAwareInterface {

    public function render($data=NULL) {
        /** @var ItemViewModel $itemViewModel */
        $itemViewModel = $this->viewModel;
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
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', ($itemViewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolveClass($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    ],
                 'data-red-onpath'=>$itemViewModel->isOnPath() ? "1" : "0",
                 'data-red-leaf'=>$itemViewModel->isLeaf() ? "1" : "0",
                 'data-red-depth'=>$itemViewModel->getRealDepth()
                ],
                $innerHtml);
        return $html;
    }
}
