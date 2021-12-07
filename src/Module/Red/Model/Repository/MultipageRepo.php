<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\EntityInterface;
use Red\Model\Entity\MultipageInterface;
use Red\Model\Entity\Multipage;
use Red\Model\Dao\MultipageDao;
use Model\Dao\DaoChildInterface;
use Red\Model\Hydrator\MultipageHydrator;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MultipageRepo extends RepoAbstract implements MultipageRepoInterface {

    /**
     * @var DaoChildInterface
     */
    protected $dao;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

    public function __construct(MultipageDao $multipageDao, MultipageHydrator $multipageHydrator) {
        $this->dataManager = $multipageDao;
        $this->registerHydrator($multipageHydrator);
    }

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
     * @param type $menuItemIdFk
     * @return MultipageInterface|null
     */
    public function getByReference($menuItemIdFk): ?EntityInterface {
        return $this->getEntityByReference($menuItemIdFk);
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
    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(MultipageInterface $multipage) {
        return $multipage->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
