<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\Renderer\Html\Manage\ButtonsMenuRendererAbstract;
use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsItemManipulationRenderer extends ButtonsMenuRendererAbstract {
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
}
