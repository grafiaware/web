<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuAddMultilevelRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(ItemViewModelInterface $viewModel) {
        $buttons[] = $this->expandButtons([$this->getButtonAdd($viewModel), $this->getButtonAddChild($viewModel)], $this->classMap->get('Icons', 'icon.plus'));
        return $buttons;
    }


}
