<?php
namespace  Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;
use Component\View\Menu\ItemComponentInterface;

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

//    private function renderItemManipulationButtons(MenuItemInterface $menuItem) {
//        $buttons[] = $this->getButtonActive($menuItem);
//        $buttons[] = $this->getButtonTrash($menuItem);
//        return $buttons;
//    }
//
//    private function renderMenuManipulationButtons(MenuItemInterface $menuItem) {
//        $buttons[] = $this->expandButtons($this->getButtonAdd($menuItem), $this->classMap->get('Icons', 'icon.plus'));
//        $buttons[] = $this->expandButtons($this->getButtonCut($menuItem).$this->getButtonCopy($menuItem), $this->classMap->get('Icons', 'icon.object'));
//        return $buttons;
//    }
//
//    private function expandButtons($expandedButtons, $expandsionIconClass) {
//        return
//            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsChangeView')],
//                Html::tag('i', ['class'=>$expandsionIconClass])
//                .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttonsChangeViewGroup')],
//                    $expandedButtons
//                )
//            );
//    }
//
//    private function renderPasteButtons(MenuItemInterface $menuItem) {
//        $buttons[] = $this->getButtonPaste($menuItem);
//        $buttons[] = $this->getButtonCutted($menuItem);
//        return $buttons;
//    }
//
//    private function renderCuttedItemButtons(MenuItemInterface $menuItem) {
//        $buttons[] = $this->getButtonCutted($menuItem);
//        return $buttons;
//    }
//
//    private function getButtonActive(MenuItemInterface $menuItem) {
//        $active = $menuItem->getActive();
//        return Html::tag('button',
//                ['class'=>$this->classMap->get('Buttons', 'button'),
//                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/menu/{$menuItem->getUidFk()}/toggle",
//                ],
//                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
//            );
//    }
//    private function getButtonAdd(MenuItemInterface $menuItem) {
//        return Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button'),
//                'data-tooltip'=>'Přidat sourozence',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/add",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
//            )
//            .
//            Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button'),
//                'data-tooltip'=>'Přidat potomka',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/addchild",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
//            );
//    }
//    private function getButtonPaste(MenuItemInterface $menuItem) {
//        return Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button.paste'),
//                'data-tooltip'=>'Vložit jako sourozence',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/paste",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
//            )
//            .
//            Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button.paste'),
//                'data-tooltip'=>'Vložit jako potomka',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/pastechild",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
//            );
//    }
//
//    // i pro trash
//    protected function getButtonCut(MenuItemInterface $menuItem) {
//        return  Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button'),
//                'data-tooltip'=>'Vybrat k přesunutí',
//                'data-position'=>'top right',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cut",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cut')])
//            );
//    }
//    private function getButtonCopy(MenuItemInterface $menuItem) {
//        return  Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button'),
//                'data-tooltip'=>'Zkopírovat položku',
//                'data-position'=>'top right',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/copy",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.copy')])
//            );
//    }
//    private function getButtonCutted(MenuItemInterface $menuItem) {
//        return  Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button'),
//                'data-tooltip'=>'Zrušit přesunutí',
//                'data-position'=>'top right',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cutcopyescape",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cutted')])
//            );
//    }
//    private function getButtonTrash(MenuItemInterface $menuItem) {
//        return Html::tag('button', [
//                'class'=>$this->classMap->get('Buttons', 'button'),
//                'data-tooltip'=>'Odstranit položku',
//                'data-position'=>'top right',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/trash",
//                'onclick'=>"return confirm('Jste si jisti?');"
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
//            );
//    }
}
