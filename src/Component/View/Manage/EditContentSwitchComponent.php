<?php
namespace Component\View\Manage;

use Component\View\StatusComponentAbstract;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\Manage\EditContentSwitchRenderer;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

use Pes\View\ViewInterface;
use Pes\View\InheritDataViewInterface;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class EditContentSwitchComponent extends StatusComponentAbstract implements InheritDataViewInterface {

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
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class],
        ];
    }
}
