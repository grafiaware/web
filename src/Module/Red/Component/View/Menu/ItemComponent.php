<?php
namespace Red\Component\View\Menu;

use Component\View\ComponentCompositeAbstract;
use Red\Component\View\Menu\ItemComponentInterface;
use Component\View\ComponentPrototypeInterface;


/**
 * Description of ItemComponent
 *
 * ItemComponent je typu CompositeViewInterface - komponentní view se přidávají se jménem metodou appendComponentView().
 *
 * Možnosti:
 * - item, který je list v grafu položek menu (nemá pod sebou položky další úrovně) - nemá žádný komponent
 * - item, který není list - má jako komponent LevelComponent
 * - item, který je editable - má jako komponent ItemButtonsComponent
 * - item, který není list a je editable má dva komponenty
 *
 * @author pes2704
 */
class ItemComponent extends ComponentCompositeAbstract implements ItemComponentInterface {
//class ItemComponent extends ComponentCompositeAbstract implements ComponentItemPrototypeInterface {
    public static function getComponentPermissions(): array {
        return [];
    }

    public function __clone() {
        ;
    }
}
