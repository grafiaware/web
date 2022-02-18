<?php
namespace Component\View\Menu\Item;

use Access\Enum\AllowedViewEnum;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

/**
 * Description of ItemComponent
 *
 * @author pes2704
 */
class ItemComponent implements ItemComponentInterface {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Pokud je eneruje PHP template z názvu template a použije ji.
     */
    public function beforeRenderingHook(): void {
        if($this->contextData->isMenuEditable() AND $this->isAllowed(AllowedViewEnum::EDIT)) {
            $this->contextData->isPresented();
        }
    }
}
