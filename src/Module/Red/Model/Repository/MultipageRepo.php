<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\PersistableEntityInterface;
use Red\Model\Entity\MultipageInterface;
use Red\Model\Entity\Multipage;
use Red\Model\Dao\MultipageDao;
use Model\Dao\DaoReferenceUniqueInterface;
use Red\Model\Hydrator\MultipageHydrator;

use \Model\Repository\RepoAssotiatedOneTrait;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MultipageRepo extends RepoAbstract implements MultipageRepoInterface {

    /**
     * @var DaoReferenceUniqueInterface
     */
    protected $dataManager;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

    public function __construct(MultipageDao $multipageDao, MultipageHydrator $multipageHydrator) {
        $this->dataManager = $multipageDao;
        $this->registerHydrator($multipageHydrator);
    }

    use RepoAssotiatedOneTrait;

    /**
     *
     * @param type $id
     * @return MultipageInterface|null
     */
    public function get($id): ?MultipageInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $menuItemId
     * @return MultipageInterface|null
     */
    public function getByMenuItemId($menuItemId): ?MultipageInterface {
        return $this->getEntityByReference(MultipageDao::REFERENCE_MENU_ITEM, $menuItemId);
    }

    public function add(MultipageInterface $multipage) {
        $this->addEntity($multipage);
    }

    public function remove(MultipageInterface $multipage) {
        $this->removeEntity($multipage);
    }

    protected function createEntity() {
        return new Multipage();
    }

    protected function indexFromEntity(MultipageInterface $multipage) {
        return $multipage->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
