<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\ViewModel\Menu\ItemViewModelInterface;

/**
 * Description of ButtonsMenuCutCopyEscapeRenderer
 *
 * @author pes2704
 */
class ButtonsMenuCutCopyEscapeRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(ItemViewModelInterface $viewModel) {
        $buttons[] = $this->getButtonCutCopyEscape($viewModel);
        return $buttons;
    }

}
