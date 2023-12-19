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
     * @param type $assetId
     * @return MenuItemAssetInterface|null
     */
    public function get($menuItemId, $assetId): ?MenuItemAssetInterface ;

    /**
     *
     * @param type $menuItemId
     * @return MenuItemAssetInterface[]
     */
    public function findByMenuItemId($menuItemId) : array;

    /**
     *
     * @param type $assetId
     * @return MenuItemAssetInterface[]
     */
    public function findByAssetId($assetId) : array;
    
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
