<?php
namespace Red\Component\View\Manage;

use Component\View\ComponentCompositeAbstract;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

use Pes\View\ViewInterface;
use Pes\View\InheritViewModelInterface;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class EditContentSwitchComponent extends ComponentCompositeAbstract implements InheritViewModelInterface {

    /**
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritViewModel(iterable $data): ViewInterface {
        return $this->setData($data);
    }

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
        ];
    }
    
    public function getString() {
        return parent::getString();
    }
}
