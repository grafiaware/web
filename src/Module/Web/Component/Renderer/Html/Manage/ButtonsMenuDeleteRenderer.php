<?php
namespace Web\Component\Renderer\Html\Manage;

use Red\Model\Entity\MenuItemInterface;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuDeleteRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonDelete($menuItem);
        return $buttons;
    }

}
