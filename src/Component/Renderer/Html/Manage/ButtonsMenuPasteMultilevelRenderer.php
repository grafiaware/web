<?php
namespace Component\Renderer\Html\Manage;

use Component\ViewModel\Menu\Item\ItemViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuPasteMultilevelRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->expandButtons([$this->getButtonPaste($menuItem), $this->getButtonPasteChild($menuItem)], $this->classMap->get('Icons', 'icon.plus'));
        return $buttons;
    }


}