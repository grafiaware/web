<?php
namespace Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;
use Model\Entity\HierarchyNodeInterface;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

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
class ItemBlockEditableRenderer extends HtmlRendererAbstract {


    public function render($data=NULL) {
        return $this->privateRender($data);
    }

    private function privateRender(ItemViewModel $itemViewModel=NULL) {
        $menuNode = $itemViewModel->getMenuNode();
        $menuItem = $menuNode->getMenuItem();

        $ii = Html::tag('i', ['class'=> $this->classMap->resolveClass($menuItem->getActive(), 'Item',
                                    $menuItem->getActual() ? 'li i2.published' : 'li i2.notactual',
                                    $menuItem->getActual() ?  'li i2.notactive' : 'li i2.notactivenotactual'
                                    )]);

        $innerHtml =
//             "<a contenteditable=false class=\"editable\" tabindex=0 data-original-title=\"{$item['title']}\" data-uid=\"{$item['uid']}\" href=\"/menu/item/{$item['uid']}/\">{$item['title']}</a>"

                    Html::tag('a',[
                        'class'=>$this->classMap->getClass('Item', 'li a'),   // class - editable v kontejneru
                        'href'=>"www/item/{$menuNode->getMenuItem()->getLangCodeFk()}/{$menuNode->getUid()}",
                        'tabindex'=>0,
                        'data-original-title'=>$menuItem->getTitle(),
                        'data-uid'=>$menuNode->getUid(),


                        ],  // class - editable
//                            'li i1.published' => 'grafia active green',
//                            'li i1.notpublished' => 'grafia active red ',
//                            'li i2.published' => 'grafia actual grey',
//                            'li i2.notactive' => 'grafia actual yellow',
//                            'li i2.notactual' => 'grafia actual orange',
//                            'li i2.notactivenotactual' => 'grafia actual red',
                            $menuItem->getTitle()
                            .Html::tag('div', ['class'=>$this->classMap->getClass('Item', 'li div')],
                                Html::tag('i', ['class'=> $this->classMap->resolveClass(($menuItem->getActive() AND $menuItem->getActual()), 'Item', 'li div i1.published', 'li div i1.notpublished')])
                                .Html::tag('i', ['class'=> $this->classMap->resolveClass($menuItem->getActive(), 'Item',
                                        $menuItem->getActual() ? 'li div i2.published' : 'li div i2.notactual',
                                        $menuItem->getActual() ?  'li div i2.notactive' : 'li div i2.notactivenotactual'
                                        )])
                            )
                    )
                    .Html::tag('i', ['class'=>$this->classMap->resolveClass($itemViewModel->getInnerHtml(), 'Item', 'li i')])
                    .(($itemViewModel->getIsPresented() AND !$itemViewModel->getReadonly()) ? $this->renderButtons($menuNode) : '')
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

    private function renderButtons(HierarchyNodeInterface $menuNode) {

        return
        Html::tag('div', ['class'=>$this->classMap->getClass('Buttons', 'div')],
            Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'delete',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button1 i'),])
            )
            .Html::tag('button',
                ['class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Aktivní/neaktivní stránka',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'toggle',
                'formmethod'=>'post',
                'formaction'=>"api/v1/menu/{$menuNode->getUid()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolveClass($menuNode->getMenuItem()->getActive(), 'Buttons', 'div button2 i.on', 'div button2 i.off')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('Buttons', 'div button'),
                'data-tooltip'=>'Přidat sourozence',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'add',
                'formmethod'=>'post',
                'formaction'=>"api/v1/hierarchy/{$menuNode->getUid()}/add",
                    ],
                Html::tag('i', ['class'=>$this->classMap->getClass('Buttons', 'div button3 i')])
            )
        );
    }
}