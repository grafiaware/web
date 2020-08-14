<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;
use Model\Entity\HierarchyNodeInterface;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

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

    private function privateRender(ItemViewModel $itemViewModel=NULL) {
        $menuNode = $itemViewModel->getMenuNode();
        $menuItem = $menuNode->getMenuItem();
        $presentedEditable = ($itemViewModel->isPresented() AND !$itemViewModel->isReadonly());
        $active = $menuItem->getActive();
        $pastedUid = $itemViewModel->getModeCommand()['paste'] ?? '';
        $pasteMode = $pastedUid AND ($pastedUid != $menuNode->getUid());  //zobrazí v modu "paste" buttony pokud je vybrána položka pro paste, ale není to právě ta vybraná položka

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
                    ], $menuItem->getTitle())
                .Html::tag('div', ['class'=>$this->classMap->getClass('Item', 'li div')],
                    Html::tag('i', [
                        'class'=> $this->classMap->resolveClass($active, 'Item', 'li div i1.published', 'li div i1.notpublished'),
                        'title'=> $active ? "published" :  "not published"
                        ])
                )
            );
        $innerHtml[] = Html::tag('i',
                    [
                    'class'=>$this->classMap->resolveClass($itemViewModel->getInnerHtml(), 'Item', 'li i')
                    ]);
        if ($presentedEditable) {
            $innerHtml[] = Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.menu')],
                $pasteMode ? $this->renderPasteButtons($menuNode, $pastedUid) : $this->renderButtons($menuNode)
            );
        }
        $innerHtml[] = $itemViewModel->getInnerHtml();

        $html = Html::tag(     'li',
                ['class'=>[
                    $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isPresented(), 'Item', 'li.presented', 'li'),
                    ],
                 'style'=> $itemViewModel->isCutted() ? 'background-color: orange' : ''
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(HierarchyNodeInterface $menuNode) {
        $buttons[] = $this->getButtonActive($menuNode);
        $buttons[] = $this->getButtonAdd($menuNode);
        $buttons[] = $this->getButtonMove($menuNode);
        $buttons[] = $this->getButtonTrash($menuNode);
//        $buttons[] = $this->getButtonsDiv2($menuNode);

        return implode(PHP_EOL, $buttons);
    }

    private function renderPasteButtons(HierarchyNodeInterface $menuNode, $pastedUid) {
        $buttons[] = $this->getButtonActive($menuNode);
        $buttons[] = $this->getButtonPaste($menuNode, $pastedUid);
        $buttons[] = $this->getButtonMove($menuNode);
        $buttons[] = $this->getButtonTrash($menuNode);
//        $buttons[] = $this->getButtonsDiv2($menuNode);

        return implode(PHP_EOL, $buttons);
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
    private function getButtonActive(HierarchyNodeInterface $menuNode) {
        $active = $menuNode->getMenuItem()->getActive();
        return Html::tag('button',
                ['class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolveClass($active, 'Buttons', 'div button2 i.on', 'div button2 i.off')])
            );
    }
    private function getButtonAdd(HierarchyNodeInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button3 i')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button4 i')])
            );
    }
    private function getButtonPaste(HierarchyNodeInterface $menuNode, $pastedUid) {
        return Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Vložit jako sourozence',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/paste/$pastedUid",
                'style'=>'background-color: salmon',
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button3 i')])
            )
            .
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Vložit jako potomka',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/pastechild/$pastedUid",
                'style'=>'background-color: salmon',
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button4 i')])
            );
    }
    private function getButtonMove(HierarchyNodeInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"api/v1/menu/{$menuNode->getUid()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button5 i')])
            );
    }
    private function getButtonTrash(HierarchyNodeInterface $menuNode) {
        return Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button1 i'),])
            );
    }

    private function getButtonsDiv2($param) {
        $buttonsDiv2 =
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Uložit',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button6 i')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Převzít titulek článku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button7 i')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Zrušit úpravy',
                'data-position'=>'top right',
                //'type'=>'submit',
                //'formmethod'=>'post',
                //'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                'onclick'=>'event.preventDefault();close_edit_name()'
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button8 i')])
            );
         Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.name')],
        );

    }

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
