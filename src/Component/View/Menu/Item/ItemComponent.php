<?php
namespace Component\View\Menu\Item;

use Component\View\ComponentAbstract;

use Access\Enum\AccessPresentationEnum;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

/**
 * Description of ItemComponent
 *
 * @author pes2704
 */
class ItemComponent extends ComponentAbstract implements ItemComponentInterface {

    /**
     *
     * @var ItemViewModelInterface
     */
    protected $contextData;

}
