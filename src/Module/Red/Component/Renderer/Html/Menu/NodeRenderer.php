<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;

use Red\Model\Entity\MenuItemInterface;
use Red\Component\ViewModel\Menu\NodeViewModelInterface;
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
class NodeRenderer extends HtmlRendererAbstract {

    /**
     *
     * @var NodeViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderNoneditableItem($viewModel);
    }

    private function renderNoneditableItem(NodeViewModelInterface $viewModel) {
        $levelHtml = ($viewModel->offsetExists(NodeComponentInterface::LEVEL)) ? $viewModel->offsetGet(NodeComponentInterface::LEVEL) : "";
        $driverHtml = ($viewModel->offsetExists(NodeComponentInterface::DRIVER)) ? $viewModel->offsetGet(NodeComponentInterface::DRIVER) : "";
        $html = Html::tag('li',
                ['class'=>[
                    $this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li.leaf', ($viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                        //TODO: DRIVER
//                    $this->classMap->resolve($viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-styleinfo'=> $this->redLiEditableStyle($viewModel)
               ],
               [
                $driverHtml,$levelHtml,
                Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
               ]
            );
        return $html;
    }

    private function redLiEditableStyle(NodeViewModelInterface $viewModel) {
        return [
                ($viewModel->isLeaf() ? "leaf " : ""),
                ($viewModel->isOnPath() ? "onpath " : ""),
                ("realDepth:".$viewModel->getRealDepth()." "),
                (($viewModel->getRealDepth() == 1) ? "dropdown " : ""),
            ];
    }
}
