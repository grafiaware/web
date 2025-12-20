<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Red\Model\Entity\MenuItemAsset;
use Red\Model\Entity\Asset;
use Red\Model\Entity\AssetInterface;
use Red\Model\Entity\MenuItemAssetAggregateAssetInterface;

/**
 * Description of PaperPaperContentsAggregate
 *
 * @author pes2704
 */
class MenuItemAssetAggregateAsset extends MenuItemAsset implements MenuItemAssetAggregateAssetInterface {

    /**
     * @var AssetInterface array of
     */
    private $assets = [];

    /**
     *
     * @param int $id id asset
     * @return AssetInterface|null
     */
    public function getAsset($id): ?AssetInterface {
        return array_key_exists($id, $this->assets) ? $this->assets[$id] : null;
    }

    /**
     *
     * @return AssetInterface array of
     */
    public function getAssetsArray(): array {
        return $this->assets;
    }

    /**
     *
     * @param array $assets
     * @return MenuItemAssetAggregateAssetInterface
     */
    public function setAssetsArray(array $assets=[]): MenuItemAssetAggregateAssetInterface {
        $this->assets = $assets;
        return $this;
    }

}
