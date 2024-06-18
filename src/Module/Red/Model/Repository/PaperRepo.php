<?php
namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\Paper;
use Red\Model\Dao\PaperDao;
use Model\Dao\DaoReferenceUniqueInterface;
use Red\Model\Hydrator\PaperHydrator;

use \Model\Repository\RepoAssotiatedOneTrait;
//use \Model\Repository\RepoAssociatedWithJoinOneTrait;
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

    use RepoAssotiatedOneTrait;
//    use RepoAssociatedWithJoinOneTrait;
    /**
     *
     * @param type $id
     * @return PaperInterface|null
     */
    public function get($id): ?PaperInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $menuItemId
     * @return PaperInterface|null
     */
    public function getByMenuItemId($menuItemId): ?PaperInterface {
        return $this->getEntityByReference(PaperDao::REFERENCE_MENU_ITEM, $menuItemId);
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
