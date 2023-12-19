<?php
namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;
use Red\Model\Repository\AssetRepoInterface;

use Red\Model\Entity\AssetInterface;
use Red\Model\Entity\Asset;
use Red\Model\Dao\AssetDaoInterface;
use Red\Model\Hydrator\AssetHydrator;

//use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class AssetRepo extends RepoAbstract implements AssetRepoInterface {

    public function __construct(AssetDaoInterface $assetDao, AssetHydrator $assetHydrator) {
        $this->dataManager = $assetDao;
        $this->registerHydrator($assetHydrator);
    }

    /**
     *
     * @param type $id
     * @return AssetInterface|null
     */
    public function get($id): ?AssetInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $filepath
     * @return AssetInterface[]
     */
    public function findByFilename($filepath): array {
        return $this->findEntities("filepath = :filepath", [":filepath"=>$filepath]);
    }

    /**
     *
     * @return AssetInterface[]
     */
    public function findAll() : array {
        return $this->findEntities();
    }

    /**
     *
     * @param AssetInterface $asset
     * @return void
     */
    public function add(AssetInterface $asset) :void {
        $this->addEntity($asset);
    }

    /**
     *
     * @param AssetInterface $asset
     * @return void
     */
    public function remove(AssetInterface $asset) :void {
        $this->removeEntity($asset);
    }

    protected function createEntity() {
        return new Asset();
    }

    protected function indexFromEntity(AssetInterface $asset) {
       return $asset->getId() ;
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
