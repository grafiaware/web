<?php
namespace Component\Renderer\Html\Manage;

use Red\Model\Entity\MenuItemInterface;

/**
 * Description of ButtonsMenuCutCopyEscapeRenderer
 *
 * @author pes2704
 */
class ButtonsMenuCutCopyEscapeRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->getButtonCutCopyEscape($menuItem);
        return $buttons;
    }

}
