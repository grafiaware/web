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
        $this->dao = $multipageDao;
        $this->registerHydrator($multipageHydrator);
    }

    /**
     *
     * @param type $id
     * @return MultipageInterface|null
     */
    public function get($id): ?MultipageInterface {
        $index = $id;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($id));
        }
        return $this->collection[$index] ?? NULL;
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return MultipageInterface|null
     */
    public function getByReference($menuItemIdFk): ?EntityInterface {
        $row = $this->dao->getByFk($menuItemIdFk);
        $index = $this->indexFromRow($row);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $row);
        }
        return $this->collection[$index] ?? NULL;
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
