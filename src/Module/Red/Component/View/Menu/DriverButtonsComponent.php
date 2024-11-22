<?php
namespace Red\Component\View\Menu;

use Component\View\ComponentCollectionAbstract;
use Pes\View\InheritViewModelInterface;
use Pes\View\ViewInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of ItemButtonsComponent
 *
 * ItemButtonsComponent je kontejner na button komponenty - je typu CollectionViewInterface, komponentní view se přidávají jako kolekce (iterable bez pojmenování prvků)
 *
 * @author pes2704
 */
class DriverButtonsComponent extends ComponentCollectionAbstract implements InheritViewModelInterface {

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            
        ];
    }
    
    public function inheritViewModel(iterable $data): ViewInterface {
        $this->setData($data);
        return $this;
    }
}
