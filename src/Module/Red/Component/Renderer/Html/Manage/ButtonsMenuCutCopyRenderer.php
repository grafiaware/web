<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\ViewModel\Menu\ItemViewModelInterface;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuCutCopyRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(ItemViewModelInterface $viewModel) {
        $buttons[] = $this->expandButtons([$this->getButtonCut($viewModel), $this->getButtonCopy($viewModel)], $this->classMap->get('Icons', 'icon.object'));
        return $buttons;
    }

}
