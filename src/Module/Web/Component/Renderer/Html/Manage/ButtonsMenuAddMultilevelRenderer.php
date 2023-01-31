<?php
namespace Web\Component\Renderer\Html\Manage;

use Web\Component\ViewModel\Menu\Item\ItemViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

use Pes\Text\Html;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuAddMultilevelRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->expandButtons([$this->getButtonAdd($menuItem), $this->getButtonAddChild($menuItem)], $this->classMap->get('Icons', 'icon.plus'));
        return $buttons;
    }


}
