<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\Renderer\Html\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModelInterface;
use Red\Component\ViewModel\Content\Authored\Paper\NamedPaperViewModelInterface;

use Model\Entity\MenuItemAggregateHierarchyInterface;
use Model\Entity\MenuItemAggregatePaperInterface;
use Pes\Text\Html;

/**
 * Description of HeadlinedEditableRenderer
 *
 * @author pes2704
 */
class HeadlinedEditableRenderer extends HtmlRendererAbstract {
    public function render($data=NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperViewModelInterface $viewModel) {
        $menuNode = $viewModel->getHierarchyNode();
        $paper = $viewModel->getMenuItemPaperAggregate();
        if ($viewModel instanceof NamedPaperViewModelInterface) {
            $name = "named: ".$viewModel->getComponentAggregate()->getName();
        } else {
            $name = "presented";
        }

        if (isset($menuNode) AND isset($paper)) {

            // TinyMCE v inline režimu pojmenovává proměnné v POSTu mce_XX, kde XX je asi pořadové číslo selected elementu na celé stránce
            // takže to znamená buď používat form tak, že obsahuje vždy jen jednu proměnnou a pak mce_cokoliv jsou zaslaná data
            // nebo přidat editovanému elementu id, pak TinyMCE použije toto id. Použije ho tak, že přidá tag <input type=hidden name=id_editovaného_elementu> a této proměnné přiřadí hodnotu.
            // Jenže id elementu musí být unikátní na stránce, proto přidávám paper id -> na serveru pak se dá hledané jméno proměnné v postu složit ze stringu "content_"
            // a parametru v rest, který obsahuje paper id.

            $innerHtml =
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/{$paper->getMenuItemIdFk()}/headline"],
                    $this->renderButtons($menuNode, $paper)
                    .Html::tag('div', ['class'=>$this->classMap->get('Component', 'div div div')],
                        Html::tag('headline', ['id'=>"headline_{$paper->getMenuItemIdFk()}", 'class'=>$this->classMap->get('Component', 'div div div headline')],
                            $paper->getPaper()
                        )
                        .Html::tag('i', ['class'=> $this->classMap->resolve(($menuNode->getHierarchyAggregate()->selectActive() AND $menuNode->getHierarchyAggregate()->selectActual()), 'Component',
                                'div div div i1.published', 'div div div i1.notpublished')]
                        )
                        .Html::tag('i', ['class'=> $this->classMap->resolve($menuNode->getHierarchyAggregate()->selectActive(), 'Component',
                                $menuNode->getHierarchyAggregate()->selectActual() ? 'div div div i2.published' : 'div div div i2.notactual',
                                $menuNode->getHierarchyAggregate()->selectActual() ?  'div div div i2.notactive' : 'div div div i2.notactivenotactual'
                                )]
                        )
                        //.Html::tag('i', ['class'=>$this->classMap->get('Component', 'div div div i3')])
                    )
                    .Html::tag('content', ['id'=>"content_{$paper->getMenuItemIdFk()}", 'class'=>$this->classMap->get('Component', 'div div content')], $paper->getPaperSection())
                );
        } else {
            $innerHtml = Html::tag('div', [], 'No data item or article for rendering.');
        }
        // atribut data-component je jen pro info v html
        return Html::tag('div', ['data-component'=>$name, 'class'=>$this->classMap->get('Component', 'div')],
                Html::tag('div', ['class'=>$this->classMap->get('Component', 'div div')], $innerHtml)
            );
    }

    private function renderButtons(MenuItemAggregateHierarchyInterface $menuNode, MenuItemAggregatePaperInterface $paper) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
            return
            Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div')],
                Html::tag('button',
                            ['class'=>$this->classMap->get('Buttons', 'div button'),
                            'data-tooltip'=>'Aktivní/neaktivní stránka',
                            'data-position'=>'bottom center',
                            'type'=>'submit',
                            'name'=>'toggle',
                            'formmethod'=>'post',
                            'formaction'=>"red/v1/menu/{$menuNode->getUid()}/toggle",
                            ],
                    Html::tag('i', ['class'=>$this->classMap->resolve($menuNode->getHierarchy()->isActive(), 'Buttons', 'div button5 i.on', 'div button5 i.off')])
                )
                .Html::tag('div',
                            ['class'=>$this->classMap->get('Buttons', 'div div'),
                            'data-tooltip'=>'Změnit od '.$menuNode->getHierarchy()->getShowTime().' do '.$menuNode->getHierarchy()->getHideTime(), 'data-position'=>'bottom center',
                            ],
                    Html::tag('i', ['class'=>$this->classMap->get('Buttons', 'div div i')])
                    .Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div div div')],
                        Html::tag('p', ['class'=>$this->classMap->get('Buttons', 'div div div p')], 'Od')
                        .Html::tagNopair('input', ['type'=>'date', 'name'=>''])
                        .Html::tag('button', ['class'=>$this->classMap->get('Buttons', 'div div div button'), 'name'=>''], 'Trvale')
                        .Html::tag('p',['class'=>$this->classMap->get('Buttons', 'div div div p')], 'Do')
                        .Html::tagNopair('input', ['type'=>'date', 'name'=>''])
                        .Html::tag('button', ['class'=>$this->classMap->get('Buttons', 'div div div button'), 'name'=>''], 'Trvale')
                .Html::tag('button',
                                    ['class'=>$this->classMap->get('Buttons', 'div div div button'),
                            'type'=>'submit',
                            'name'=>'time',
                            'formmethod'=>'post',
                            'formaction'=>"red/v1/menu/{$menuNode->getUid()}/time",
                                    ], 'Uložit'
                )

                    )
                )
            );
    }
}


//        return "<div>"
//            . "<a contenteditable=false class=\"editable\" tabindex=0 data-original-title=\"{$item['title']}\" data-uid=\"{$item['uid']}\" href=\"menu/item/{$item['uid']}/\">{$item['title']}</a>"
//                . "<div>"
//                    . "<span>{$item['depth']}</span>"
//                    . "<button type=\"submit\" name=\"delete\" class=\"\" formmethod=\"post\" formaction=\"menu/delete/{$item['uid']}/\"  onclick=\"return confirm('Jste si jisti?');\">X</button>"
//                    . "<button type=\"submit\" name=\"add\" class=\"\" formmethod=\"post\" formaction=\"menu/add/{$item['uid']}/\">+</button>"
//                    . "<button type=\"submit\" name=\"addchild\" class=\"\" formmethod=\"post\" formaction=\"menu/addchild/{$item['uid']}/\">+></button>"
//                . "</div>"
//            . "</div>";