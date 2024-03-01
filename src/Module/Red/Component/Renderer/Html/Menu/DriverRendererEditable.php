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
 * Description of DriverRendererEditable
 *
 * @author pes2704
 */
class DriverRendererEditable extends HtmlRendererAbstract {

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

        if ($viewModel->isPresented()) {
            $buttonsHtml = $viewModel->offsetExists(ItemComponentInterface::ITEM_BUTTONS) ? $viewModel->offsetGet(ItemComponentInterface::ITEM_BUTTONS) : "";

            $itemHtml =
                Html::tag('form', [],
                    Html::tag('p',
                        [
                        'class'=>[
                            $this->classMap->get('Item', 'li a'),   // class - 'editable' v kontejneru
                            $this->classMap->resolve($viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                            ],
                        'data-href'=>$viewModel->getPageHref(),
                        'data-red-content-api-uri'=> $viewModel->getRedApiUri(),
                        'tabindex'=>0,
                        ],

                        // POZOR: závislost na edit.js
                        // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem) acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
                        // t.j. selektor vybírá <span>, který má rodiče <p>
                        Html::tag('span', [
                            'contenteditable'=> "true",
                            'data-original-title'=>$viewModel->getTitle(),
                            ],
                            $viewModel->getTitle()
                            .Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                        )
                        . $semafor
                    )
                    .Html::tag('div',
                        ['class'=>$this->classMap->get('Buttons', 'div.buttons')],
                        $buttonsHtml)
                );
        } else {
            $itemHtml = Html::tag('a',
                [
                    'class'=>[
                        $this->classMap->get('Item', 'li a'),
                        $this->classMap->get('Item', 'li'),
                        ],
                    'href'=>$viewModel->getPageHref(),
                    'data-red-content-api-uri'=> $viewModel->getRedApiUri(),
                ],
                Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                    $viewModel->getTitle()
                    .Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                )
                . $semafor
            );
        }

        $liHtml = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li.leaf', ($viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    $this->classMap->resolve($viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiEditableStyle($viewModel)

                ],
                [
                    $itemHtml,
                    $levelHtml
                ]
            );


        return $liHtml;
    }

    private function redLiEditableStyle($viewModel) {
        return
            ($viewModel->isOnPath() ? "onpath " : "")
            .(($viewModel->getRealDepth() == 1) ? "dropdown " : "")
            .($viewModel->isPresented() ? "presented " : "")
            .($viewModel->isCutted() ? "cutted " : "") ;
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
