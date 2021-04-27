<?php
namespace  Component\Renderer\Html\Authored\Menu;

use Component\Renderer\Html\HtmlModelRendererAbstract;
use Component\ViewModel\Authored\Menu\Item\ItemViewModelInterface;
use Model\Entity\HierarchyAggregateInterface;

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
                    $this->classMap->resolveClass($this->viewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolveClass($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    $this->classMap->resolveClass($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-info'=>
                    ($this->viewModel->isOnPath() ? "isOnPath " : "")
                    .($this->viewModel->isLeaf() ? "isLeaf " : "")
                    .($this->viewModel->isPresented() ? "isPresented " : "")
                    .($this->viewModel->isCutted() ? "isCutted " : "")
                    ."realDepth: ".$this->viewModel->getRealDepth()                ],
                $this->renderNonEditableInner()
                );
        return $html;

    }

    private function renderNonEditableInner() {
        $menuNode = $this->viewModel->getMenuNode();
        $innerHtml = Html::tag('a', ['class'=>$this->classMap->getClass('Item', 'li a'), 'href'=> "www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}" ],
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

        $presentedEditable = ($this->viewModel->isPresented() AND $this->viewModel->isEditableItem());
        $active = $menuItem->getActive();
        $pasteMode = $this->viewModel->isPasteMode();
        $cutted = $this->viewModel->isCutted();
//        $pasteMode = $pastedUid AND ($pastedUid != $menuNode->getUid());  //zobrazí v modu "paste" buttony pokud je vybrána položka pro paste, ale není to právě ta vybraná položka

        $innerHtml[] =
            Html::tag($presentedEditable ? 'p' : 'a',
                [
                'class'=>$this->classMapEditable->getClass('Item', 'li a'),   // class - 'editable' v kontejneru
                'href'=>"www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}",
                'tabindex'=>0,
                ],

                // POZOR: závislost na edit.js
                // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem) acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
                // vyvírá <span>, který má rodiče <p>
                Html::tag('span', [
                    'contenteditable'=> ($presentedEditable ? "true" : "false"),
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

        $buttonsHtml = '';
        if ($presentedEditable) {
            if ($pasteMode) {
                if($cutted) {
                    $buttonsHtml = $this->renderCuttedItemButtons($menuNode);
                } else {
                    $buttonsHtml = $this->renderPasteButtons($menuNode, $this->viewModel->getPasteUid());
                }
            } else {
                $buttonsHtml = $this->renderButtons($menuNode);
            }
        } else {
            if ($pasteMode AND $cutted) {
                $buttonsHtml = $this->renderCuttedItemButtons($menuNode);
            }
        }
        $innerHtml[] = $buttonsHtml ? Html::tag('div', ['class'=>$this->classMapEditable->getClass('Buttons', 'div.buttons')], $buttonsHtml) : '';

        $innerHtml[] = $this->viewModel->getInnerHtml();

        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMapEditable->resolveClass($this->viewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMapEditable->resolveClass($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMapEditable->resolveClass($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    $this->classMapEditable->resolveClass($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                 'data-red-info'=>
                    ($this->viewModel->isOnPath() ? "isOnPath " : "")
                    .($this->viewModel->isLeaf() ? "isLeaf " : "")
                    .($this->viewModel->isPresented() ? "isPresented " : "")
                    .($this->viewModel->isCutted() ? "isCutted " : "")
                    ."realDepth: ".$this->viewModel->getRealDepth()
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(HierarchyAggregateInterface $menuNode) {
        $buttons[] = $this->getButtonActive($menuNode);
        $buttons[] = $this->getButtonAdd($menuNode);
        $buttons[] = $this->getButtonCut($menuNode);
        $buttons[] = $this->getButtonTrash($menuNode);
        return $buttons;
    }

    private function renderPasteButtons(HierarchyAggregateInterface $menuNode, $pastedUid) {
        $buttons[] = $this->getButtonPaste($menuNode, $pastedUid);
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
                'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
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
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.addchildren')])
            );
    }
    private function getButtonPaste(HierarchyAggregateInterface $menuNode, $pastedUid) {
        return Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/paste/$pastedUid",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMapEditable->getClass('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/pastechild/$pastedUid",
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
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/cut",
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
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/cut",
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
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->getClass('Buttons', 'button.movetotrash'),])
            );
    }
}
