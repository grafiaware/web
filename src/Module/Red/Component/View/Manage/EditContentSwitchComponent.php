<?php
namespace Red\Component\View\Manage;

use Red\Component\View\ComponentAbstract;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

use Pes\View\ViewInterface;
use Pes\View\InheritDataViewInterface;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class EditContentSwitchComponent extends ComponentAbstract implements InheritDataViewInterface {

    /**
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritData(iterable $data): ViewInterface {
        return $this->setData($data);
    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
        ];
    }
}