<?php
namespace  Component\Renderer\Html\Authored\Menu;

use Component\Renderer\Html\HtmlModelRendererAbstract;
use Component\ViewModel\Authored\Menu\Item\ItemViewModelInterface;
use Red\Model\Entity\HierarchyAggregateInterface;

use Pes\View\Renderer\RendererModelAwareInterface;
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
class ItemRenderer extends HtmlModelRendererAbstract implements RendererModelAwareInterface {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $viewModel;

    public function render($data=NULL) {
        if ($this->viewModel->isEditableItem()) {
            return $this->renderEditable();
        } else {
            return $this->renderNoneditable();
        }
    }

    private function renderNoneditable() {
        // sloučeno
        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolveClass($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolveClass($this->viewModel->isCutted(), 'Item', 'li.cut', 'li'),
                    $this->classMap->resolveClass($this->viewModel->isOnPath(), 'Item', $this->viewModel->isLeaf() ? 'li' : 'li.parent' , 'li')
                    ],
                 'data-red-style'=> $this->redLiStyle()
               ],
                $this->renderNonEditableInner()
                );
        return $html;

    }

    private function renderNonEditableInner() {
        $menuNode = $this->viewModel->getMenuNode();
        $innerHtml = Html::tag('a',
                    ['class'=>[
                        $this->classMap->getClass('Item', 'li a'),
                        $this->classMap->resolveClass($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                        ],
                    'href'=> "web/v1/page/item/{$menuNode->getUid()}" ],
                        Html::tag('span', ['class'=>$this->classMap->getClass('Item', 'li a span')],
                            $menuNode->getMenuItem()->getTitle()
                            .Html::tag('i', ['class'=>$this->classMap->resolveClass($this->viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                        )
                    )
                    .$this->viewModel->getInnerHtml();
        return $innerHtml;
    }

    protected function renderEditable() {
        $menuNode = $this->viewModel->getMenuNode();
        $menuItem = $menuNode->getMenuItem();

        $active = $menuItem->getActive();

        $liInnerHtml[] =
            Html::tag('p',
                [
                'class'=>[
                    $this->classMapEditable->getClass('Item', 'li a'),   // class - 'editable' v kontejneru
                    $this->classMap->resolveClass($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    ],
                'href'=>"web/v1/page/item/{$menuNode->getUid()}",
                'tabindex'=>0,
                ],

                // POZOR: závislost na edit.js
                // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem) acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
                // vyvírá <span>, který má rodiče <p>
                Html::tag('span', [
                    'contenteditable'=> "true",
                    'data-original-title'=>$menuItem->getTitle(),
                    'data-uid'=>$menuNode->getUid(),
                    ],
                    $menuItem->getTitle()
                    .Html::tag('i', ['class'=>$this->classMapEditable->resolveClass($this->viewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])
                )
                .Html::tag('span', ['class'=>$this->classMapEditable->getClass('Item', 'semafor')],
                    Html::tag('i', [
                        'class'=> $this->classMapEditable->resolveClass($active, 'Item', 'semafor.published', 'semafor.notpublished'),
                        'title'=> $active ? "published" :  "not published"
                        ])
                )

            );

        $liInnerHtml[] = Html::tag('div',
                ['class'=>$this->classMapEditable->getClass('Buttons', 'div.buttons')],
                $this->renderButtons($menuNode));

        $liInnerHtml[] = $this->viewModel->getInnerHtml();

        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMapEditable->resolveClass($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMapEditable->resolveClass($this->viewModel->isCutted(), 'Item', 'li.cut', 'li'),
                    $this->classMap->resolveClass($this->viewModel->isOnPath(), 'Item', $this->viewModel->isLeaf() ? 'li' : 'li.parent' , 'li')
                    ],
                 'data-red-style'=> $this->redLiStyle()

                ],
                $liInnerHtml);
        return $html;
    }

    private function redLiStyle() {
        return ($this->viewModel->isOnPath() ? "onpath " : "")
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
                ['class'=>$this->classMapEditable->getClass('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuNode->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMapEditable->resolveClass($active, 'Buttons', 'button.notpublish', 'button.publish')])
            );
    }
    private function getButtonAdd(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.addchildren')])
            );
    }
    private function getButtonPaste(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/paste",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/pastechild",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.addchildren')])
            );
    }
    private function getButtonCut(HierarchyAggregateInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.cut')])
            );
    }
    private function getButtonCutted(HierarchyAggregateInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/cutescape",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.cutted')])
            );
    }
    private function getButtonTrash(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.movetotrash'),])
            );
    }
}
