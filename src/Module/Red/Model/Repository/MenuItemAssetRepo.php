<?php
namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;
use Red\Model\Repository\MenuItemAssetRepoInterface;

use Red\Model\Entity\MenuItemAssetInterface;
use Red\Model\Entity\MenuItemAsset;
use Red\Model\Dao\MenuItemAssetDao;
use Red\Model\Hydrator\MenuItemAssetHydrator;

//use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuItemAssetRepo extends RepoAbstract implements MenuItemAssetRepoInterface {

    public function __construct(MenuItemAssetDao $menuItemAssetDao, MenuItemAssetHydrator $menuItemAssetHydrator) {
        $this->dataManager = $menuItemAssetDao;
        $this->registerHydrator($menuItemAssetHydrator);
    }

    /**
     * 
     * @param type $menuItemId
     * @param type $assetId
     * @return MenuItemAssetInterface|null
     */
    public function get($menuItemId, $assetId): ?MenuItemAssetInterface {
        return $this->getEntity($menuItemId, $assetId);
    }

    /**
     *
     * @param type $menuItemId
     * @return MenuItemAssetInterface[]
     */
    public function findByMenuItemId($menuItemId): array {
        return $this->findEntities("menu_item_id_fk = :menu_item_id_fk", [":menu_item_id_fk"=>$menuItemId]);
    }
    
    /**
     * 
     * @param type $assetId
     * @return MenuItemAssetInterface[]
     */
    public function findByAssetId($assetId): array {
        return $this->findEntities("asset_id_fk = :asset_id_fk", [":asset_id_fk"=>$assetId]);
    }
    
    /**
     *
     * @return MenuItemAssetInterface[]
     */
    public function findAll() : array {
        return $this->findEntities();
    }

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @return void
     */
    public function add(MenuItemAssetInterface $menuItemAsset) :void {
        $this->addEntity($menuItemAsset);
    }

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @return void
     */
    public function remove(MenuItemAssetInterface $menuItemAsset) :void {
        $this->removeEntity($menuItemAsset);
    }

    protected function createEntity() {
        return new MenuItemAsset();
    }

    protected function indexFromEntity(MenuItemAssetInterface $menuItemAsset) {
       return $menuItemAsset->getMenuItemIdFk().$menuItemAsset->getAssetIdFk() ;
    }

    protected function indexFromRow($row) {
        return $row['menu_item_id_fk'].$row['asset_id_fk'];
    }
}
