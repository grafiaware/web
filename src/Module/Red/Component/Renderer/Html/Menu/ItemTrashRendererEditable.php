<?php
namespace  Red\Component\Renderer\Html\Menu;

use Pes\Text\Html;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Component\ViewModel\Menu\Item\ItemViewModelInterface;

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
    protected function renderEditableItem(ItemViewModelInterface $viewModel) {
        $menuItem = $viewModel->getHierarchyAggregate()->getMenuItem();
        $presentedEditable = ($viewModel->isPresented() AND $viewModel->isMenuEditable());
//        $active = $menuItem->getActive();
        $pasteMode = $viewModel->isPasteMode();
        $cutted = $viewModel->isCutted();

        // element a s potomkem span - needitovalný titulek
        $innerHtml[] = Html::tag('a', [
                        'class'=>[
                            $this->classMap->get('Item', 'li a'),
                            $this->classMap->resolve($viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                            ],
                        'href'=>"web/v1/page/item/{$menuItem->getUidFk()}",
                         ],
                        $menuItem->getTitle()
//                        .Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
//                            Html::tag('i', [
//                                'class'=> $this->classMap->get('Icons', 'semafor.trashed'),
//                                'title'=> "smazaná položka"
//                                ])
//                        )
                    );
        $innerHtml[] = Html::tag('i', ['class'=>$this->classMap->resolve($viewModel->getChild(), 'Item', 'li.isnotleaf icon', 'li.leaf')]);

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

        $innerHtml[] = $buttonsHtml ? Html::tag('form', [], Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.buttons')], $buttonsHtml)) : '';
        $innerHtml[] = $viewModel->getChild();

        $liClass = ['class'=>[
                    (string) $this->classMap->resolve($viewModel->isLeaf(), 'Item', 'li.leaf', ($viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    (string) $this->classMap->resolve($viewModel->isCutted(), 'Item', 'li.cut', 'li')
                    ],
                ];
        $innerHtml = implode('', $innerHtml);
        $html = Html::tag('li', $liClass, $innerHtml);


       return $html;
    }

    private function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonCut($menuItem);
        $buttons[] = $this->getButtonDelete($menuItem);
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

}
