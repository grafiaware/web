<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;

use Red\Model\Entity\MenuItemInterface;
use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Red\Component\View\Menu\NodeComponentInterface;

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
class ItemRenderer extends HtmlRendererAbstract {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderNoneditableItem($viewModel);
    }

    private function renderNoneditableItem(ItemViewModelInterface $viewModel) {
        $anchorHtml = Html::tag('a',
                [
                    'class'=>[
                        $this->classMap->get('Item', 'li a'),
                        $this->classMap->resolve($viewModel->isPresented(), 'Item', 'li.presented', 'li'),   
                        ],
                    'href'=> $viewModel->getPageHref(),
                    'data-red-content-api-uri'=> $viewModel->getRedApiUri()
                ],
                Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                    $viewModel->getTitle()
                        //TODO: DRIVER
//                    .Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                )
            );

        return $anchorHtml;
    }

    private function redLiEditableStyle($viewModel) {
        return [
            $viewModel->isLeaf() ? "leaf " : "",
            $viewModel->isOnPath() ? "onpath " : "",
            "realDepth:".$viewModel->getRealDepth()." ",
            ($viewModel->getRealDepth() == 1) ? "dropdown " : "",
            $viewModel->isPresented() ? "presented " : "",
            $viewModel->isPresented() ? "presented " : "",                
            $viewModel->isCutted() ? "cutted " : "",
        ];
    }
}
