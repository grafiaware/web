<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Red\Component\View\Menu\ItemComponentInterface;

use Red\Model\Entity\MenuItemInterface;
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
class ItemRendererEditable extends HtmlRendererAbstract {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderEditableItem($viewModel);
    }

    protected function renderEditableItem(ItemViewModelInterface $viewModel) {
        $semafor = $viewModel->isMenuEditable() ? $this->semafor($viewModel) : "";
        $levelHtml = ($viewModel->offsetExists(ItemComponentInterface::LEVEL)) ? $viewModel->offsetGet(ItemComponentInterface::LEVEL) : "";
        $driverHtml = ($viewModel->offsetExists(ItemComponentInterface::DRIVER)) ? $viewModel->offsetGet(ItemComponentInterface::DRIVER) : "";

        $liHtml = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li.leaf', ($viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                        //TODO: DRIVER
//                    $this->classMap->resolve($viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiEditableStyle($viewModel)

                ],
                [
                    $driverHtml,
                    $levelHtml
                ]
            );


        return $liHtml;
    }

    private function redLiEditableStyle($viewModel) {
        return [
                ($viewModel->isOnPath() ? "onpath " : ""),
                (($viewModel->getRealDepth() == 1) ? "dropdown " : ""),
                ($viewModel->isCutted() ? "cutted " : ""),
            ];
    }

    private function semafor(ItemViewModelInterface $viewModel) {
        $active = $viewModel->isActive();
        $html = Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
                    Html::tag('i', [
                        'class'=> $this->classMap->resolve($active, 'Icons', 'semafor.published', 'semafor.notpublished'),
                        'title'=> $active ? "published" :  "not published"
                        ])
                );
        return $html;
    }
}
