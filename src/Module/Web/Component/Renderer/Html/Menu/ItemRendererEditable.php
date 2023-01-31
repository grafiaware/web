<?php
namespace  Web\Component\Renderer\Html\Menu;

use Web\Component\Renderer\Html\HtmlRendererAbstract;
use Web\Component\ViewModel\Menu\Item\ItemViewModelInterface;
use Web\Component\View\Menu\ItemComponentInterface;

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
        $menuItem = $viewModel->getHierarchyAggregate()->getMenuItem();
        $semafor = $viewModel->isMenuEditable() ? $this->semafor($menuItem) : "";
        $levelHtml = ($viewModel->offsetExists(ItemComponentInterface::LEVEL)) ? $viewModel->offsetGet(ItemComponentInterface::LEVEL) : "";

        if ($viewModel->isPresented()) {
//            $buttonsHtml = '';
//            if ($viewModel->isPasteMode()) {
//                if($viewModel->isCutted()) {
//                    $buttonsHtml = $this->renderCuttedItemButtons($menuItem);
//                } else {
//                    $buttonsHtml = $this->renderPasteButtons($menuItem);
//                }
//            } else {
//                $buttonsHtml = array_merge($this->renderItemManipulationButtons($menuItem),$this->renderMenuManipulationButtons($menuItem));
//            }

            $buttonsHtml = $viewModel->offsetExists(ItemComponentInterface::ITEM_BUTTONS) ? $viewModel->offsetGet(ItemComponentInterface::ITEM_BUTTONS) : "";

            $liInnerHtml[] =
                Html::tag('form', [],
                    Html::tag('p',
                        [
                        'class'=>[
                            $this->classMap->get('Item', 'li a'),   // class - 'editable' v kontejneru
                            $this->classMap->resolve($viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                            ],
                        'data-href'=>"web/v1/page/item/{$menuItem->getUidFk()}",
                        'tabindex'=>0,
                        ],

                        // POZOR: závislost na edit.js
                        // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem) acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
                        // t.j. selektor vybírá <span>, který má rodiče <p>
                        Html::tag('span', [
                            'contenteditable'=> "true",
                            'data-original-title'=>$menuItem->getTitle(),
                            'data-uid'=>$menuItem->getUidFk(),
                            ],
                            $menuItem->getTitle()
                            .Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                        )
                        . $semafor
                    )
                    .Html::tag('div',
                        ['class'=>$this->classMap->get('Buttons', 'div.buttons')],
                        $buttonsHtml)
                );
        } else {
            $liInnerHtml[] = Html::tag('a',
                [
                    'class'=>[
                        $this->classMap->get('Item', 'li a'),
                        $this->classMap->resolve($viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                        ],
                    'href'=> "web/v1/page/item/{$menuItem->getUidFk()}"
                ],
                Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                    $menuItem->getTitle()
                    .Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                )
                . $semafor
            );
        }
        $liInnerHtml[] = $levelHtml;

        $liHtml = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li.leaf', ($viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    $this->classMap->resolve($viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiEditableStyle($viewModel)

                ],
                $liInnerHtml);


        return $liHtml;
    }

    private function redLiEditableStyle($viewModel) {
        return
            ($viewModel->isOnPath() ? "onpath " : "")
            .(($viewModel->getRealDepth() == 1) ? "dropdown " : "")
            .($viewModel->isPresented() ? "presented " : "")
            .($viewModel->isCutted() ? "cutted " : "") ;
    }

    private function semafor(MenuItemInterface $menuItem) {
        $active =$menuItem->getActive();
        $html = Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
                    Html::tag('i', [
                        'class'=> $this->classMap->resolve($active, 'Icons', 'semafor.published', 'semafor.notpublished'),
                        'title'=> $active ? "published" :  "not published"
                        ])
                );
        return $html;
    }
}
