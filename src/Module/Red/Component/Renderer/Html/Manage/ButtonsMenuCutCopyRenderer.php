<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\ViewModel\Menu\DriverViewModelInterface;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuCutCopyRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(DriverViewModelInterface $viewModel) {
        $buttons[] = $this->expandButtons([$this->getButtonCut($viewModel), $this->getButtonCopy($viewModel)], $this->classMap->get('Icons', 'icon.object'));
        return $buttons;
    }

}
