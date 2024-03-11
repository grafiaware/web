<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\ViewModel\Menu\ItemViewModelInterface;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuDeleteRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(ItemViewModelInterface $viewModel) {
        $buttons[] = $this->getButtonDelete($viewModel);
        return $buttons;
    }

}
