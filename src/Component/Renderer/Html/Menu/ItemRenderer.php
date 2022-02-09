<?php
namespace  Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\HierarchyAggregateInterface;

use Pes\Text\Html;

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

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    public function render($viewModel=NULL) {
        $this->viewModel = $viewModel;
        $menuItem = $this->viewModel->getHierarchyAggregate()->getMenuItem();

            return $this->renderNoneditableItem($menuItem);
    }

    private function renderNoneditableItem(MenuItemInterface $menuItem) {
        $semafor = $this->viewModel->isMenuEditable() ? $this->semafor($menuItem) : "";
        $innerHtml = Html::tag('a',
                        [
                            'class'=>[
                                $this->classMap->get('Item', 'li a'),
                                $this->classMap->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                                ],
                            'href'=> "web/v1/page/item/{$menuItem->getUidFk()}"
                        ],
                        Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                            $menuItem->getTitle()
                            .Html::tag('i', ['class'=>$this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                        )
                        . $semafor
                    )
                    .$this->viewModel->getInnerHtml();
        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($this->viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    $this->classMap->resolve($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiEditableStyle()
               ],
                $innerHtml
                );
        return $html;

    }

    private function semafor(MenuItemInterface $menuItem) {
        $active =$menuItem->getActive();
        return Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
                    Html::tag('i', [
                        'class'=> $this->classMap->resolve($active, 'Icons', 'semafor.published', 'semafor.notpublished'),
                        'title'=> $active ? "published" :  "not published"
                        ])
                );
    }
    private function redLiEditableStyle() {
        return
            ($this->viewModel->isOnPath() ? "onpath " : "")
            .(($this->viewModel->getRealDepth() == 1) ? "dropdown " : "")
            .($this->viewModel->isPresented() ? "presented " : "")
            .($this->viewModel->isCutted() ? "cutted " : "") ;
    }
}
