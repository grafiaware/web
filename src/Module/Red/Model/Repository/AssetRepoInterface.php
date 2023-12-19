<?php
namespace Red\Model\Repository;

use Model\Repository\RepoInterface;

use Red\Model\Entity\AssetInterface;

/**
 *
 *
 */
interface AssetRepoInterface  extends RepoInterface {
    /**
     * 
     * @param type $id
     * @return AssetInterface|null
     */
    public function get($id): ?AssetInterface ;

    /**
     *
     * @param type $filepath
     * @return AssetInterface[]
     */
    public function findByFilename($filepath): array;
    
    /**
     *
     * @return AssetInterface[]
     */
    public function findAll(): array;

    /**
     *
     * @param AssetInterface $menuItemAsset
     * @return void
     */
    public function add(AssetInterface $menuItemAsset) :void;

    /**
     *
     * @param AssetInterface $menuItemAsset
     * @return void
     */
    public function remove(AssetInterface $menuItemAsset)  :void;


}
