<?php
namespace Red\Model\Entity;

use Pes\Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemAssetInterface extends PersistableEntityInterface {

    public function getMenuItemIdFk();
    public function getAssetIdFk();
    public function setMenuItemIdFk($menuItemIdFk): MenuItemAssetInterface;
    public function setAssetIdFk($assetIdTk): MenuItemAssetInterface;
}
