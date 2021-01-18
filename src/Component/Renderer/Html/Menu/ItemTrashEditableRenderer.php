<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;
use Model\Entity\HierarchyAggregateInterface;
use Component\ViewModel\Authored\Menu\Item\ItemViewModelInterface;

use Pes\Utils\Directory;

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
class ItemTrashEditableRenderer extends HtmlRendererAbstract {


    public function render($data=NULL) {
        return $this->privateRender($data);
    }

    private function privateRender(ItemViewModelInterface $itemViewModel=NULL) {
        $menuNode = $itemViewModel->getMenuNode();
        $menuItem = $menuNode->getMenuItem();

        $presentedEditable = ($itemViewModel->isPresented() AND !$itemViewModel->isReadonly());
        $active = $menuItem->getActive();
        $pasteMode = $itemViewModel->isPasteMode();
        $cutted = $itemViewModel->isCutted();

        // element a s potomkem span - needitovalný titulek
        $innerHtml[] = Html::tag('a', [
                        'class'=>$this->classMap->getClass('Item', 'li a'),
                        'href'=>"www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}",
                         ],
                        $menuNode->getMenuItem()->getTitle()
                        .Html::tag('span', ['class'=>$this->classMap->getClass('Item', 'semafor')],
                            Html::tag('i', [
                                'class'=> $this->classMap->getClass('Item', 'semafor.trashed'),
                                'title'=> "smazaná položka"
                                ])
                        )
                    );
        $innerHtml[] = Html::tag('i', ['class'=>$this->classMap->resolveClass($itemViewModel->getInnerHtml(), 'Item', 'li.isnotleaf icon')]);

        $buttonsHtml = '';
        if ($presentedEditable) {
            if ($pasteMode) {
                if($cutted) {
                    $buttonsHtml = $this->renderCuttedItemButtons($menuNode);
                }
            } else {
                $buttonsHtml = $this->renderButtons($menuNode);
            }
        } else {
            if ($pasteMode AND $cutted) {
                $buttonsHtml = $this->renderCuttedItemButtons($menuNode);
            }
        }

        $innerHtml[] = $buttonsHtml ? Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.buttons')], $buttonsHtml) : '';
        $innerHtml[] = $itemViewModel->getInnerHtml();

        $html = Html::tag('li',
                ['class'=>[
                    $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', ($itemViewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolveClass($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(HierarchyAggregateInterface $menuNode) {
        $buttons[] = $this->getButtonCut($menuNode);
        $buttons[] = $this->getButtonDelete($menuNode);
        return $buttons;
    }

    private function renderCuttedItemButtons(HierarchyAggregateInterface $menuNode) {
        $buttons[] = $this->getButtonCutted($menuNode);
        return $buttons;
    }

    private function getButtonDelete(HierarchyAggregateInterface $menuNode) {
        return
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Trvale odstranit',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/delete",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.icons'),],
                        Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.delete'),])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.exclamation'),])
                )
            );
    }

    private function getButtonCut(HierarchyAggregateInterface $menuNode) {
        return
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/cut",

                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.cut')])
            );
    }
    private function getButtonCutted(HierarchyAggregateInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.cutted')])
            );
    }

}
