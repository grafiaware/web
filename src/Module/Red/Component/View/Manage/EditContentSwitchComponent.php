<?php
namespace Red\Component\View\Manage;

use Component\View\ComponentCompositeAbstract;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

use Pes\View\ViewInterface;
use Pes\View\InheritDataViewInterface;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class EditContentSwitchComponent extends ComponentCompositeAbstract implements InheritDataViewInterface {

    /**
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritData(iterable $data): ViewInterface {
        return $this->setData($data);
    }

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
        ];
    }
}
