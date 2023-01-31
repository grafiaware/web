<?php
namespace Web\Component\Renderer\Html\Manage;

use Red\Model\Entity\MenuItemInterface;

/**
 * Description of ButtonsItemManipulationRenderer
 *
 * @author pes2704
 */
class ButtonsMenuCutCopyRenderer extends ButtonsMenuRendererAbstract {

    protected function renderButtons(MenuItemInterface $menuItem) {
        $buttons[] = $this->expandButtons([$this->getButtonCut($menuItem), $this->getButtonCopy($menuItem)], $this->classMap->get('Icons', 'icon.object'));
        return $buttons;
    }

}
