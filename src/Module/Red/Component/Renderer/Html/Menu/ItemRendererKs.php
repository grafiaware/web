<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;

use Red\Model\Entity\MenuItemInterface;
use Red\Component\ViewModel\Menu\Item\ItemViewModelInterface;
use Red\Component\View\Menu\ItemComponentInterface;

use Pes\Type\ContextDataInterface;

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
class ItemRendererKs extends HtmlRendererAbstract {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderNoneditableItem($viewModel);
    }

    private function renderNoneditableItem(ItemViewModelInterface $viewModel) {
        $menuItem = $viewModel->getHierarchyAggregate()->getMenuItem();
        $levelHtml = ($viewModel->offsetExists(ItemComponentInterface::LEVEL)) ? $viewModel->offsetGet(ItemComponentInterface::LEVEL) : "";
        $innerHtml = Html::tag('a',
                        [
                            'class'=>[
                                $this->classMap->get('Item', 'li a'),
                                $this->classMap->resolve($viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                                ],
                            'href'=> "web/v1/page/item/{$menuItem->getUidFk()}"
                        ],
                        Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                            $menuItem->getTitle()
                        )
                    )
                    .$levelHtml;
        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li.leaf', ($viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    $this->classMap->resolve($viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiEditableStyle($viewModel)
               ],
                $innerHtml,
                Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                );
        return $html;
    }

    private function redLiEditableStyle($viewModel) {
        return
            ($viewModel->isLeaf() ? "leaf " : "")
            .($viewModel->isOnPath() ? "onpath " : "")
            .("realDepth:".$viewModel->getRealDepth()." ")
            .(($viewModel->getRealDepth() == 1) ? "dropdown " : "")
            .($viewModel->isPresented() ? "presented " : "")
            .($viewModel->isCutted() ? "cutted " : "") ;
    }
}
