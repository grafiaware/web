<?php
namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Red\Model\Entity\MenuItemAssetInterface;

/**
 * Description of MenuItemAsset
 *
 * @author pes2704
 */
class MenuItemAsset extends PersistableEntityAbstract implements MenuItemAssetInterface {

    private $menuItemIdFk;
    private $assetIdFk;

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getAssetIdFk() {
        return $this->assetIdFk;
    }

    public function setMenuItemIdFk($menuItemIdFk): MenuItemAssetInterface {
        $this->menuItemIdFk = $menuItemIdFk;
        return $this;
    }

    public function setAssetIdFk($assetIdTk): MenuItemAssetInterface {
        $this->assetIdFk = $assetIdTk;
        return $this;
    }
}
