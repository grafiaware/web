<?php
namespace  Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;
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
        if ($viewModel->isEditableItem()) {
            return $this->renderEditableItem();
        } else {
            return $this->renderNoneditableItem();
        }
    }

    private function renderNoneditableItem() {
        // sloučeno
        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($this->viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    $this->classMap->resolve($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiStyle()
               ],
                $this->renderNonEditableInner()
                );
        return $html;

    }

    private function renderNonEditableInner() {
        $menuNode = $this->viewModel->getMenuNode();
        $dropdown = Html::tag('i', ['class'=>$this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')]);
        $semafor = $this->viewModel->isMenuEditable() ? $this->semafor() : "";
        $innerHtml = Html::tag('a',
                        [
                            'class'=>[
                                $this->classMap->get('Item', 'li a'),
                                $this->classMap->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                                ],
                            'href'=> "web/v1/page/item/{$menuNode->getUid()}"
                        ],
                        Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                            $menuNode->getMenuItem()->getTitle()
                            .Html::tag('i', ['class'=>$this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                        )
                        . $semafor
                    )
                    .$this->viewModel->getInnerHtml();
        return $innerHtml;
    }

    protected function renderEditableItem() {
        $menuNode = $this->viewModel->getMenuNode();

        $liInnerHtml[] =
            Html::tag('p',
                [
                'class'=>[
                    $this->classMapEditable->get('Item', 'li a'),   // class - 'editable' v kontejneru
                    $this->classMapEditable->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    ],
                'href'=>"web/v1/page/item/{$menuNode->getUid()}",
                'tabindex'=>0,
                ],

                // POZOR: závislost na edit.js
                // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem) acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
                // vyvírá <span>, který má rodiče <p>
                Html::tag('span', [
                    'contenteditable'=> "true",
                    'data-original-title'=>$menuNode->getMenuItem()->getTitle(),
                    'data-uid'=>$menuNode->getUid(),
                    ],
                    $menuNode->getMenuItem()->getTitle()
                    .Html::tag('i', ['class'=>$this->classMapEditable->resolve($this->viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                )
                .$this->semafor()

            );

        $liInnerHtml[] = Html::tag('div',
                ['class'=>$this->classMapEditable->get('CommonButtons', 'div.buttons')],
                $this->renderButtons($menuNode));

        $liInnerHtml[] = $this->viewModel->getInnerHtml();

        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMapEditable->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($this->viewModel->isOnPath(), 'Item', 'li.parent', 'li'),
                    $this->classMapEditable->resolve($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-style'=> $this->redLiStyle()

                ],
                $liInnerHtml);
        return $html;
    }

    private function semafor() {
        $active = $this->viewModel->getMenuNode()->getMenuItem()->getActive();
        return Html::tag('span', ['class'=>$this->classMapEditable->get('Item', 'semafor')],
                    Html::tag('i', [
                        'class'=> $this->classMapEditable->resolve($active, 'Item', 'semafor.published', 'semafor.notpublished'),
                        'title'=> $active ? "published" :  "not published"
                        ])
                );
    }

    private function redLiStyle() {
        return
            ($this->viewModel->isEditableItem() ? "editable-item " : "noneditable-item ")
            .($this->viewModel->isOnPath() ? "onpath " : "")
            .(($this->viewModel->getRealDepth() == 1) ? "dropdown " : "")
            .($this->viewModel->isPresented() ? "presented " : "")
            .($this->viewModel->isCutted() ? "cutted " : "") ;
    }

    private function redLiIStyle() {
        return ($this->viewModel->isLeaf() ? "leaf " : "");
    }

    private function renderButtons(HierarchyAggregateInterface $menuNode) {
        $buttonsHtml = '';

        if ($this->viewModel->isPasteMode()) {
            if($this->viewModel->isCutted()) {
                $buttonsHtml = $this->renderCuttedItemButtons($menuNode);
            } else {
                $buttonsHtml = $this->renderPasteButtons($menuNode);
            }
        } else {
            $buttonsHtml = $this->renderStandardButtons($menuNode);
        }
        return $buttonsHtml;
    }

    private function renderStandardButtons(HierarchyAggregateInterface $menuNode) {
        $buttons[] = $this->getButtonActive($menuNode);
        $buttons[] = $this->getButtonAdd($menuNode);
        $buttons[] = $this->getButtonCut($menuNode);
        $buttons[] = $this->getButtonTrash($menuNode);
        return $buttons;
    }

    private function renderPasteButtons(HierarchyAggregateInterface $menuNode) {
        $buttons[] = $this->getButtonPaste($menuNode);
        $buttons[] = $this->getButtonCutted($menuNode);
        return $buttons;
    }

    private function renderCuttedItemButtons(HierarchyAggregateInterface $menuNode) {
        $buttons[] = $this->getButtonCutted($menuNode);
        return $buttons;
    }

    private function getButtonActive(HierarchyAggregateInterface $menuNode) {
        $active = $menuNode->getMenuItem()->getActive();
        return Html::tag('button',
                ['class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuNode->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMapEditable->resolve($active, 'CommonButtons', 'button.notpublish', 'button.publish')])
            );
    }
    private function getButtonAdd(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('Buttons', 'button.addchildren')])
            );
    }
    private function getButtonPaste(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMapEditable->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/paste",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMapEditable->get('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/pastechild",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('Buttons', 'button.addchildren')])
            );
    }
    private function getButtonCut(HierarchyAggregateInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.cut')])
            );
    }
    private function getButtonCutted(HierarchyAggregateInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/cutescape",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.cutted')])
            );
    }
    private function getButtonTrash(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.movetotrash')])
            );
    }
}
