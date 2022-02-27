<?php
namespace Component\ViewModel\Authored\Manage;

use Pes\Type\ContextData;

use Red\Model\Entity\ItemActionInterface;
use Component\ViewModel\Authored\AuthoredViewModelAbstract;

/**
 * Description of ToggleEditContentButtonViewModel
 *
 * @author pes2704
 */
class ToggleEditContentButtonViewModel extends AuthoredViewModelAbstract implements ToggleEditContentButtonViewModelInterface {

    private $typeFk;
    private $itemId;

    public function setItemActionParams($typeFk, $itemId):void {
        $this->typeFk = $typeFk;
        $this->itemId = $itemId;
    }

    /**
     * Vrací akci pro položku zadanou pomocí metody setItemActionParams().
     * @return ItemActionInterface
     * @throws LogicException
     */
    public function getItemAction(): ItemActionInterface {
        if(!isset($this->typeFk) OR !isset($this->itemId)) {
            throw new LogicException("Nebyly nastaveny parametry pro výběr itemAction.");
        }
        return $this->itemActionRepo->get($this->typeFk, $this->itemId);
    }
}
