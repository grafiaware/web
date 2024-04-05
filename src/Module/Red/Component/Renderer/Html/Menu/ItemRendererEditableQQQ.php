<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Red\Component\View\Menu\ItemComponentInterface;

use Pes\Text\Html;

/**
 * Description of ItemRendererEditable
 *
 * @author pes2704
 */
class ItemRendererEditableQQQ extends HtmlRendererAbstract {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderEditableItem($viewModel);
    }

    protected function renderEditableItem(ItemViewModelInterface $viewModel) {
        $levelHtml = ($viewModel->offsetExists(ItemComponentInterface::LEVEL)) ? $viewModel->offsetGet(ItemComponentInterface::LEVEL) : "";
        $driverHtml = ($viewModel->offsetExists(ItemComponentInterface::DRIVER)) ? $viewModel->offsetGet(ItemComponentInterface::DRIVER) : "";

        $liHtml = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li.leaf', ($viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    ],
                 'data-red-styleinfo'=> $this->redLiEditableStyle($viewModel)
                ],
                [
                    $driverHtml,
                    $levelHtml,
                    Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                ]
            );
        return $liHtml;
    }

    private function redLiEditableStyle(ItemViewModelInterface $viewModel) {
        return [
                ($viewModel->isLeaf() ? "leaf " : ""),
                ($viewModel->isOnPath() ? "onpath " : ""),
                ("realDepth:".$viewModel->getRealDepth()." "),
                (($viewModel->getRealDepth() == 1) ? "dropdown " : ""),
            ];
    }
}