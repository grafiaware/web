<?php
namespace  Component\Renderer\Html\Menu;

use Pes\Text\Html;
use Red\Model\Entity\HierarchyAggregateInterface;

/**
 * Description of ItemTrashEditableRenderer
 *
 * @author pes2704
 */
class ItemTrashRenderer extends ItemRenderer {

    /**
     * Přetěžuje metodu ItemRender pro editable variantu renderování.
     *
     * @return string
     */
    protected function renderEditableItem() {
        $menuNode = $this->viewModel->getMenuNode();
        $menuItem = $menuNode->getMenuItem();

        $presentedEditable = ($this->viewModel->isPresented() AND $this->viewModel->isEditableItem());
        $active = $menuItem->getActive();
        $pasteMode = $this->viewModel->isPasteMode();
        $cutted = $this->viewModel->isCutted();

        // element a s potomkem span - needitovalný titulek
        $innerHtml[] = Html::tag('a', [
                        'class'=>[
                            $this->classMapEditable->get('Item', 'li a'),
                            $this->classMapEditable->resolve($this->viewModel->isPresented(), 'Item', 'li.presented', 'li'),
                            ],
                        'href'=>"web/v1/page/item/{$menuNode->getUid()}",
                         ],
                        $menuNode->getMenuItem()->getTitle()
                        .Html::tag('span', ['class'=>$this->classMapEditable->get('Item', 'semafor')],
                            Html::tag('i', [
                                'class'=> $this->classMapEditable->get('Item', 'semafor.trashed'),
                                'title'=> "smazaná položka"
                                ])
                        )
                    );
        $innerHtml[] = Html::tag('i', ['class'=>$this->classMapEditable->resolve($this->viewModel->getInnerHtml(), 'Item', 'li.isnotleaf icon', 'li.leaf')]);

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

        $innerHtml[] = $buttonsHtml ? Html::tag('div', ['class'=>$this->classMapEditable->get('CommonButtons', 'div.buttons')], $buttonsHtml) : '';
        $innerHtml[] = $this->viewModel->getInnerHtml();

        $html = Html::tag('li',
                ['class'=>[
                    $this->classMapEditable->resolve($this->viewModel->isLeaf(), 'Item', 'li.leaf', ($this->viewModel->getRealDepth() == 1) ? 'li.dropdown' : 'li.item'),
                    $this->classMapEditable->resolve($this->viewModel->isCutted(), 'Item', 'li.cut', 'li')
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
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Trvale odstranit',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/delete",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('Buttons', 'button.icons'),],
                        Html::tag('i', ['class'=>$this->classMapEditable->get('Buttons', 'button.delete'),])
                        .Html::tag('i', ['class'=>$this->classMapEditable->get('Buttons', 'button.exclamation'),])
                )
            );
    }

    private function getButtonCut(HierarchyAggregateInterface $menuNode) {
        return
            Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Vybrat k přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/cut",

                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.cut')])
            );
    }
    private function getButtonCutted(HierarchyAggregateInterface $menuNode) {
        return  Html::tag('button', [
                'class'=>$this->classMapEditable->get('CommonButtons', 'button'),
                'data-tooltip'=>'Zrušit přesunutí',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'move',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuNode->getUid()}/cut",
                    ],
                Html::tag('i', ['class'=>$this->classMapEditable->get('CommonButtons', 'button.cutted')])
            );
    }

}
