<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;
use Model\Entity\MenuNodeInterface;
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
        $presentedEditable = ($itemViewModel->getIsPresented() AND !$itemViewModel->getReadonly());

        $innerHtml =
                    Html::tag($presentedEditable ? 'p' : 'a',
                        [
                        'class'=>$this->classMap->getClass('Item', 'li a'),   // class - 'editable' v kontejneru
                        'href'=>"www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}",
                        'tabindex'=>0,
                        ],
                        // class - editable
//                            'li i1.published' => 'grafia active green',
//                            'li i1.notpublished' => 'grafia active red ',
//                            'li i2.published' => 'grafia actual grey',
//                            'li i2.notactive' => 'grafia actual yellow',
//                            'li i2.notactual' => 'grafia actual orange',
//                            'li i2.notactivenotactual' => 'grafia actual red',
                        Html::tag('i', [
                            'class'=> $this->classMap->resolveClass(($menuItem->getActive() AND $menuItem->getActual()), 'Item', 'li i1.published', 'li i1.notpublished'),
                            'title'=>($menuItem->getActive() AND $menuItem->getActual()) ? "published" :  "not published"
                            ])
                        .Html::tag('i',
                                [
                                'class'=> $this->classMap->resolveClass($menuItem->getActive(), 'Item',
                                        $menuItem->getActual() ? 'li i2.published' : 'li i2.notactual',
                                        $menuItem->getActual() ?  'li i2.notactive' : 'li i2.notactivenotactual'
                                    ),
                                'role'=>"presentation",
                                'title'=>$menuItem->getActive() ? ($menuItem->getActual() ? "active and actual" : "active but not actual") : ($menuItem->getActual() ? "actual but not active" : "not active nor actual")
                                ])
                        .Html::tag('span', [
                            'contenteditable'=> ($presentedEditable ? "true" : "false"),
                            'data-original-title'=>$menuItem->getTitle(),
                            'data-uid'=>$menuNode->getUid(),
                            ], $menuItem->getTitle())
                    )
                    .Html::tag('i',
                            [
                            'class'=>$this->classMap->resolveClass($itemViewModel->getInnerHtml(), 'Item', 'li i')
                            ])
                    .($presentedEditable ? $this->renderButtons($menuNode) : '')
                    .$itemViewModel->getInnerHtml();
        $html = Html::tag('li',
                ['class'=>[
                    $this->classMap->resolveClass($itemViewModel->isOnPath(), 'Item', 'li.onpath', 'li'),
                    $this->classMap->resolveClass($itemViewModel->isLeaf(), 'Item', 'li.leaf', 'li'),
                    $this->classMap->resolveClass($itemViewModel->getIsPresented(), 'Item', 'li.presented', 'li'),
                    ]
                ],
                $innerHtml);
        return $html;
    }

    private function renderButtons(MenuNodeInterface $menuNode) {

        return
        Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.menu')],
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Přejmenovat položku',
                //'type'=>'submit',
                'name'=>'add',
                //'formmethod'=>'post',
                //'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/",
                'onclick'=>'event.preventDefault();edit_name()'
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button0 i')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Odstranit položku',
                'type'=>'submit',
                'name'=>'delete',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button1 i'),])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Přidat sourozence',
                'type'=>'submit',
                'name'=>'add',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button2 i')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Přidat potomka',
                'type'=>'submit',
                'name'=>'addchild',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/addchild",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button3 i')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button4 i')])
            )
        )
        .Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div.name')],
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Uložit',
                'data-position'=>'top right',
                'type'=>'submit',
                //'name'=>'',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button5 i')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Převzít titulek článku',
                'data-position'=>'top right',
                'type'=>'submit',
                //'name'=>'',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button6 i')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Zrušit úpravy',
                'data-position'=>'top right',
                //'type'=>'submit',
                //'name'=>'',
                //'formmethod'=>'post',
                //'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/move/senPatříCílovýParentUid",
                'onclick'=>'event.preventDefault();close_edit_name()'
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button7 i')])
            )
        );
    }
}
