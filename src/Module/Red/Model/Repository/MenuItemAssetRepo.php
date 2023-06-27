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
     * @param type $id
     * @return MenuItemAssetInterface|null
     */
    public function get($menuItemId, $filepath): ?MenuItemAssetInterface {
        return $this->getEntity($menuItemId, $filepath);
    }

    /**
     *
     * @param type $menuItemId
     * @return MenuItemAssetInterface[]
     */
    public function findByMenuItemId($menuItemId): array {
        return $this->findEntities("menu_item_id_FK = :menu_item_id_FK", [":menu_item_id_FK"=>$menuItemId]);
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
     * @param MenuItemAssetInterface $manuItem
     * @return void
     */
    public function remove(MenuItemAssetInterface $manuItem) :void {
        $this->removeEntity($manuItem);
    }

    protected function createEntity() {
        return new MenuItemAsset();
    }

    protected function indexFromEntity(MenuItemAssetInterface $menuItemAsset) {
       return $menuItemAsset->getId() ;
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
