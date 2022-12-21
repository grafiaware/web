<?php
namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\PersistableEntityInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\Paper;
use Red\Model\Dao\PaperDao;
use Model\Dao\DaoReferenceUniqueInterface;
use Red\Model\Hydrator\PaperHydrator;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperRepo extends RepoAbstract implements PaperRepoInterface {

    /**
     * @var DaoReferenceUniqueInterface
     */
    protected $dataManager;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

    public function __construct(PaperDao $paperDao, PaperHydrator $paperHydrator) {
        $this->dataManager = $paperDao;
        $this->registerHydrator($paperHydrator);
    }

    /**
     *
     * @param type $id
     * @return PaperInterface|null
     */
    public function get($id): ?PaperInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return PaperInterface|null
     */
    public function getByReference($menuItemIdFk): ?PersistableEntityInterface {
        $key = $this->dataManager->getReferenceKeyTouples('menu_item_id_fk', ['menu_item_id_fk'=>$menuItemIdFk]);
        return $this->getEntityByReference('menu_item_id_fk', $key);
    }

    public function add(PaperInterface $paper) {
        $this->addEntity($paper);
    }

    public function remove(PaperInterface $paper) {
        $this->removeEntity($paper);
    }

    protected function createEntity() {
        return new Paper();
    }

    protected function indexFromEntity(PaperInterface $paper) {
        return $paper->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
