<?php
namespace Component\View\Menu;

use Component\View\ComponentCompositeAbstract;

/**
 * Description of ItemComponent
 *
 * ItemComponent je typu CompositeViewInterface - komponentní view se přidávají se jménem metodou ateendComponentView().
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

}
