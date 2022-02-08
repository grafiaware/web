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
class ItemRendererEditable extends HtmlRendererAbstract {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    public function render($viewModel=NULL) {
        $this->viewModel = $viewModel;
        $menuItem = $this->viewModel->getHierarchyAggregate()->getMenuItem();

            return $this->renderEditableItem($menuItem);
    }

    protected function renderEditableItem(MenuItemInterface $menuItem) {
        $liInnerHtml[] =
            Html::tag('p',
                [
                'class'=>[
                    $this->classMap->get('Item', 'li a'),   // class - 'editable' v kontejneru
                    $this->classMap->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    ],
                'data-href'=>"web/v1/page/item/{$menuItem->getUidFk()}",
                'tabindex'=>0,
                ],

                // POZOR: závislost na edit.js
                // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem) acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
                // vyvírá <span>, který má rodiče <p>
                Html::tag('span', [
                    'contenteditable'=> "true",
                    'data-original-title'=>$menuItem->getTitle(),
                    'data-uid'=>$menuItem->getUidFk(),
                    ],
                    $menuItem->getTitle()
                    .Html::tag('i', ['class'=>$this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                )
                .$this->semafor($menuItem)

            );

        $liInnerHtml[] = Html::tag('div',
                ['class'=>$this->classMap->get('Buttons', 'div.buttons')],
                $this->renderButtons($menuItem));

        $liInnerHtml[] = $this->viewModel->getInnerHtml();

        $html =
            Html::tag('form', [],
                Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($this->viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    $this->classMap->resolve($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiEditableStyle()

                ],
                $liInnerHtml)
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

    private function renderButtons(MenuItemInterface $menuItem) {
        $buttonsHtml = '';

        if ($this->viewModel->isPasteMode()) {
            if($this->viewModel->isCutted()) {
                $buttonsHtml = $this->renderCuttedItemButtons($menuItem);
            } else {
                $buttonsHtml = $this->renderPasteButtons($menuItem);
            }
        } else {
            $buttonsHtml = $this->renderStandardButtons($menuItem);
        }
        return $buttonsHtml;
    }

    private function renderStandardButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonActive($menuItem);
        $buttons[] = $this->getButtonAdd($menuItem);
        $buttons[] = $this->getButtonCut($menuItem);
        $buttons[] = $this->getButtonTrash($menuItem);
        return $buttons;
    }

    private function renderPasteButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonPaste($menuItem);
        $buttons[] = $this->getButtonCutted($menuItem);
        return $buttons;
    }

    private function renderCuttedItemButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonCutted($menuItem);
        return $buttons;
    }

    private function getButtonActive(MenuItemInterface $menuItem) {
        $active = $menuItem->getActive();
        return Html::tag('button',
                ['class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuItem->getUidFk()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
            );
    }
    private function getButtonAdd(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }
    private function getButtonPaste(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/paste",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/pastechild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addchildren')])
            );
    }
    private function getButtonCut(MenuItemInterface $menuItem) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cut')])
            );
    }
    private function getButtonCutted(MenuItemInterface $menuItem) {
        return  Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cutescape",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cutted')])
            );
    }
    private function getButtonTrash(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
            );
    }
}
