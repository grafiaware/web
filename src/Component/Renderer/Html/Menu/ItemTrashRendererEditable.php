<?php
namespace  Component\Renderer\Html\Menu;

use Pes\Text\Html;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 * Description of ItemTrashEditableRenderer
 *
 * @author pes2704
 */
class ItemTrashRendererEditable extends ItemRendererEditable {

    /**
     * Přetěžuje metodu ItemRender pro editable variantu renderování.
     *
     * @return string
     */
    protected function renderEditableItem(MenuItemInterface $menuItem) {
        $menuNode = $this->viewModel->getHierarchyAggregate();
        $menuItem = $menuNode->getMenuItem();

        $presentedEditable = ($this->viewModel->isPresented() AND $this->viewModel->isMenuEditable());
        $active = $menuItem->getActive();
        $pasteMode = $this->viewModel->isPasteMode();
        $cutted = $this->viewModel->isCutted();

        // element a s potomkem span - needitovalný titulek
        $innerHtml[] = Html::tag('a', [
                        'class'=>[
                            $this->classMap->get('Item', 'li a'),
                            $this->classMap->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                            ],
                        'href'=>"web/v1/page/item/{$menuNode->getUid()}",
                         ],
                        $menuNode->getMenuItem()->getTitle()
                        .Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
                            Html::tag('i', [
                                'class'=> $this->classMap->get('Icons', 'semafor.trashed'),
                                'title'=> "smazaná položka"
                                ])
                        )
                    );
        $innerHtml[] = Html::tag('i', ['class'=>$this->classMap->resolve($this->viewModel->getInnerHtml(), 'Item', 'li.isnotleaf icon', 'li.leaf')]);

        $buttonsHtml = '';
        if ($presentedEditable) {
            if ($pasteMode) {
                if($cutted) {
                    $buttonsHtml = $this->renderCuttedItemButtons($menuItem);
                }
            } else {
                $buttonsHtml = $this->renderButtons($menuItem);
            }
        } else {
            if ($pasteMode AND $cutted) {
                $buttonsHtml = $this->renderCuttedItemButtons($menuItem);
            }
        }

        $innerHtml[] = $buttonsHtml ? Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttons')], $buttonsHtml) : '';
        $innerHtml[] = $this->viewModel->getInnerHtml();

        $liClass = ['class'=>[
                    $this->classMap->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMap->resolve($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                ];
        $innerHtml = implode('', $innerHtml);
        $li = Html::tag('li',
                $liClass,
                $innerHtml);
       $html =
            Html::tag('form', [],
                    $li
            );
        return $html;
    }

    private function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonCut($menuItem);
        $buttons[] = $this->getButtonDelete($menuItem);
        return $buttons;
    }

    private function renderCuttedItemButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonCutted($menuItem);
        return $buttons;
    }

    private function getButtonDelete(MenuItemInterface $menuItem) {
        return
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Trvale odstranit',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/delete",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons'),],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.delete'),])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.exclamation'),])
                )
            );
    }

    private function getButtonCut(MenuItemInterface $menuItem) {
        return
            Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
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
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.cutted')])
            );
    }

}
