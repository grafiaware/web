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
class ItemEditableRenderer extends HtmlRendererAbstract {


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
//        $pasteMode = $pastedUid AND ($pastedUid != $menuNode->getUid());  //zobrazí v modu "paste" buttony pokud je vybrána položka pro paste, ale není to právě ta vybraná položka

        $innerHtml[] =
            Html::tag($presentedEditable ? 'p' : 'a',
                [
                'class'=>$this->classMap->getClass('Item', 'li a'),   // class - 'editable' v kontejneru
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
                    .Html::tag('i', ['class'=>$this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li i', 'li i.dropdown')])    
                )
                .Html::tag('span', ['class'=>$this->classMap->getClass('Item', 'semafor')],
                    Html::tag('i', [
                        'class'=> $this->classMap->resolveClass($active, 'Item', 'semafor.published', 'semafor.notpublished'),
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
                    $buttonsHtml = $this->renderPasteButtons($menuNode, $itemViewModel->getPasteUid());
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

        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', ($itemViewModel->getRealDepth() >= 3) ? 'li.item' : 'li.dropdown'),
                    $this->classMap->resolveClass($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
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

//    private function getButtonRename(HierarchyNodeInterface $menuNode) {

//        $buttonsDiv1 =
//            Html::tag('button', [
//                'class'=>$this->classMap->getClass('Buttons', 'div button'),
//                'data-tooltip'=>'Přejmenovat položku',
//                //'type'=>'submit',
//                'name'=>'add',
//                //'formmethod'=>'post',
//                //'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/",
//                'onclick'=>'event.preventDefault();edit_name()'
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button0 i')])
//            )
//     }
    private function getButtonActive(HierarchyAggregateInterface $menuNode) {
        $active = $menuNode->getMenuItem()->getActive();
        return Html::tag('button',
                ['class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolveClass($active, 'Buttons', 'button.notpublish', 'button.publish')])
            );
    }
    private function getButtonAdd(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.addchildren')])
            );
    }
    private function getButtonPaste(HierarchyAggregateInterface $menuNode, $pastedUid) {
        return Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/paste/$pastedUid",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.addsiblings')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button.paste'),
                'data-tooltip'=>'Vložit jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/pastechild/$pastedUid",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.addchildren')])
            );
    }
    private function getButtonCut(HierarchyAggregateInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
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
    private function getButtonTrash(HierarchyAggregateInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'button.movetotrash'),])
            );
    }

//    private function getButtonsDiv2($param) {
//        $buttonsDiv2 =
//            Html::tag('button', [
//                'class'=>$this->classMap->getClass('Buttons', 'button'),
//                'data-tooltip'=>'Uložit',
//                'data-position'=>'top right',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button6 i')])
//            )
//            .Html::tag('button', [
//                'class'=>$this->classMap->getClass('Buttons', 'button'),
//                'data-tooltip'=>'Převzít titulek článku',
//                'data-position'=>'top right',
//                'type'=>'submit',
//                'formmethod'=>'post',
//                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button7 i')])
//            )
//            .Html::tag('button', [
//                'class'=>$this->classMap->getClass('Buttons', 'button'),
//                'data-tooltip'=>'Zrušit úpravy',
//                'data-position'=>'top right',
//                //'type'=>'submit',
//                //'formmethod'=>'post',
//                //'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
//                'onclick'=>'event.preventDefault();close_edit_name()'
//                    ],
//                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button8 i')])
//            );
//         Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.name')],
//        );
//
//    }

//                    Html::tag('i', [
//                        'class'=> $this->classMap->resolveClass(($menuItem->getActive() AND $menuItem->getActual()), 'Item', 'li div i1.published', 'li div i1.notpublished'),
//                        'title'=>($menuItem->getActive() AND $menuItem->getActual()) ? "published" :  "not published"
//                        ])
//                    .Html::tag('i',
//                            [
//                            'class'=> $this->classMap->resolveClass($menuItem->getActive(), 'Item',
//                                    $menuItem->getActual() ? 'li div i2.published' : 'li div i2.notactual',
//                                    $menuItem->getActual() ?  'li div i2.notactive' : 'li div i2.notactivenotactual'
//                                ),
//                            'role'=>"presentation",
//                            'title'=>$menuItem->getActive() ? ($menuItem->getActual() ? "active and actual" : "active but not actual") : ($menuItem->getActual() ? "actual but not active" : "not active nor actual")
//                            ])

}
