<?php
namespace Red\Component\View\Manage;

use Component\View\ComponentCompositeAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ToggleEditButton
 *
 * @author pes2704
 */
class SelectTemplateComponent extends ComponentCompositeAbstract implements InheritDataViewInterface {

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
}
