<?php
namespace Red\Component\Renderer\Html\Manage;

use Red\Component\ViewModel\Menu\DriverViewModelInterface;
use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;
use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuAddRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(DriverViewModelInterface $viewModel) {
        switch ($viewModel->getItemType()) {
            // ButtonsXXX komponenty jsou typu InheritData - dědí DriverViewModel
            case ItemTypeEnum::MULTILEVEL:
                $buttons[] = $this->expandButtons([$this->getButtonAdd($viewModel), $this->getButtonAddChild($viewModel)], $this->classMap->get('Icons', 'icon.plus'));
                break;
            case ItemTypeEnum::ONELEVEL:
                $buttons[] = $this->expandButtons([$this->getButtonAdd($viewModel)], $this->classMap->get('Icons', 'icon.plus'));
                break;
            default:
                throw new LogicException("Nerozpoznán typ položek menu '$itemType'.");
        }
        return $buttons;
    }


}
