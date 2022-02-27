<?php
namespace Component\ViewModel\Authored\Manage;


use Red\Model\Entity\ItemActionInterface;
use Component\ViewModel\Authored\AuthoredViewModelInterface;


/**
 *
 * @author pes2704
 */
interface ToggleEditContentButtonViewModelInterface extends AuthoredViewModelInterface {
    public function setItemActionParams($typeFk, $itemId):void;
    public function getItemAction(): ItemActionInterface;
}
