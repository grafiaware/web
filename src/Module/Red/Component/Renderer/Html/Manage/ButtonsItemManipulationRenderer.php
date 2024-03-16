<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\Renderer\Html\Manage\ButtonsMenuRendererAbstract;
use Red\Component\ViewModel\Menu\DriverViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsItemManipulationRenderer extends ButtonsMenuRendererAbstract {
    public function render(iterable $viewModel = NULL) {
        return $this->renderButtons($viewModel);
    }

    protected function renderButtons(DriverViewModelInterface $viewModel) {
        $buttons[] = $this->getButtonActive($viewModel);
        $buttons[] = $this->getButtonTrash($viewModel);
        return $buttons;
    }
}
