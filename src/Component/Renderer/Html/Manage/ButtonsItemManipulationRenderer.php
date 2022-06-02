<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsItemManipulationRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        /** @var ItemViewModelInterface $viewModel */
        $menuItem = $viewModel->getHierarchyAggregate()->getMenuItem();
        return $this->renderButtons($menuItem);
    }

    protected function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonActive($menuItem);
        $buttons[] = $this->getButtonTrash($menuItem);
        return $buttons;
    }

    private function getButtonActive(MenuItemInterface $menuItem) {
        $active = $menuItem->getActive();
        return Html::tag('button',
                ['class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuItem->getUidFk()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'Icons', 'icon.notpublish', 'icon.publish')])
            );
    }

    private function getButtonTrash(MenuItemInterface $menuItem) {
        return Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=>'Odstranit položku',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/trash",
                'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.movetotrash')])
            );
    }
}
