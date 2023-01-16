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
    public function get($id): ?MenuItemAssetInterface {
        return $this->getEntity($id);
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
     * Vrací jednu entitu nebo null vyhledanou podle filename, filename je unikátní (je definován unique index).
     * @param type $filepath
     * @return MenuItemAssetInterface
     */
    public function getByFilename($filepath): ?MenuItemAssetInterface {
        return $this->getEntityUnique(["filepath"=>$filepath]);
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
