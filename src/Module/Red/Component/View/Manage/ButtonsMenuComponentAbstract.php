<?php
namespace Red\Component\View\Manage;

use Component\View\ComponentCollectionAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

use Pes\View\InheritViewModelInterface;
use Pes\View\ViewInterface;

/**
 * Description of ButtonsMenuComponent
 *
 * @author pes2704
 */
abstract class ButtonsMenuComponentAbstract extends ComponentCollectionAbstract implements InheritViewModelInterface {

    public function inheritViewModel(iterable $data): ViewInterface {
        $this->setData($data);
        return $this;
    }

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            
        ];
    }
}
