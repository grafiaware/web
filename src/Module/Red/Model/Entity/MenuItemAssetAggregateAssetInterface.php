<?php
namespace Red\Model\Entity;

use Red\Model\Entity\AssetInterface;
use Red\Model\Entity\MenuItemAssetInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemAssetAggregateAssetInterface extends MenuItemAssetInterface {

    /**
     * 
     * @param type $id
     * @return AssetInterface|null
     */
    public function getAsset($id): ?AssetInterface;
    
    /**
     * 
     * @return AssetInterface[]
     */
    public function getAssetsArray(): array ;
    
    /**
     *
     * @param array $assets
     * @return MenuItemAssetAggregateAssetInterface
     */
    public function setAAssetsArray(array $assets=[]): MenuItemAssetAggregateAssetInterface;    
}
