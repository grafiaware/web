<?php
namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Entity\StaticItemInterface;
use Red\Model\Entity\StaticItemClass;
use Red\Model\Dao\StaticItemDao;
use Model\Dao\DaoReferenceUniqueInterface;
use Red\Model\Hydrator\StaticItemHydrator;

use \Model\Repository\RepoAssotiatedOneTrait;

/**
 * Description of StaticRepo
 *
 * @author pes2704
 */
class StaticItemRepo extends RepoAbstract implements StaticItemRepoInterface {

    /**
     * @var DaoReferenceUniqueInterface
     */
    protected $dataManager;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

    public function __construct(StaticItemDao $staticDao, StaticItemHydrator $staticHydrator) {
        $this->dataManager = $staticDao;
        $this->registerHydrator($staticHydrator);
    }

    use RepoAssotiatedOneTrait;

    /**
     *
     * @param type $id
     * @return StaticItemInterface|null
     */
    public function get($id): ?StaticItemInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $menuItemId
     * @return StaticItemInterface|null
     */
    public function getByMenuItemId($menuItemId): ?StaticItemInterface {
        return $this->getEntityByReference(StaticItemDao::REFERENCE_MENU_ITEM, $menuItemId);
    }

    public function add(StaticItemInterface $static) {
        $this->addEntity($static);
    }

    public function remove(StaticItemInterface $static) {
        $this->removeEntity($static);
    }

    protected function createEntity() {
        return new StaticItemClass();
    }

    protected function indexFromEntity(StaticItemInterface $static) {
        return $static->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
