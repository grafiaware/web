<?php
namespace Red\Model\Repository;

use Model\Repository\RepoInterface;

use Red\Model\Entity\MenuItemAssetInterface;

/**
 *
 *
 */
interface MenuItemAssetRepoInterface  extends RepoInterface {
    /**
     * 
     * @param type $menuItemId
     * @param type $filepath
     * @return MenuItemAssetInterface|null
     */
    public function get($menuItemId, $filepath): ?MenuItemAssetInterface ;

    /**
     *
     * @param type $menuItemId
     * @return MenuItemAssetInterface[]
     */
    public function findByMenuItemId($menuItemId) : array;

    /**
     *
     * @return MenuItemAssetInterface[]
     */
    public function findAll(): array;

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @return void
     */
    public function add(MenuItemAssetInterface $menuItemAsset) :void;

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @return void
     */
    public function remove(MenuItemAssetInterface $menuItemAsset)  :void;


}
