<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\ViewModel\Menu\DriverViewModelInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuPasteOnelevelRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(DriverViewModelInterface $viewModel) {
        $buttons[] = $this->expandButtons([$this->getButtonPaste($viewModel)], $this->classMap->get('Icons', 'icon.plus'));
        return $buttons;
    }


}
