<?php
namespace Component\View\Manage;

use Component\View\ComponentCollectionAbstract;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ButtonsItemManipulationComponent
 *
 * @author pes2704
 */
class ButtonsPasteComponent extends ComponentCollectionAbstract implements InheritDataViewInterface {

    public function inheritData(iterable $data): ViewInterface {
        $this->setData($data);
        return $this;
    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
