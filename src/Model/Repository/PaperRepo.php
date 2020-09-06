<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\EntityInterface;
use Model\Entity\PaperInterface;
use Model\Entity\Paper;
use Model\Dao\PaperDao;
use Model\Dao\DaoChildInterface;
use Model\Hydrator\PaperHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperRepo extends RepoAbstract implements PaperRepoInterface {

    /**
     * @var DaoChildInterface
     */
    protected $dao;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

    public function __construct(PaperDao $paperDao, PaperHydrator $paperHydrator) {
        $this->dao = $paperDao;
        $this->registerHydrator($paperHydrator);
    }

    /**
     *
     * @param type $id
     * @return PaperInterface|null
     */
    public function get($id): ?PaperInterface {
        $index = $id;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($id));
        }
        return $this->collection[$index] ?? NULL;
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return PaperInterface|null
     */
    public function getByReference($menuItemIdFk): ?EntityInterface {
        $row = $this->dao->getByFk($menuItemIdFk);
        $index = $this->indexFromRow($row);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $row);
        }
        return $this->collection[$index] ?? NULL;
    }

    public function add(PaperInterface $paper) {
        $index = $this->indexFromEntity($paper);
        $this->addEntity($paper, $index);
    }

    public function remove(PaperInterface $paper) {
        $index = $this->indexFromEntity($paper);
        $this->removeEntity($entity, $index);
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
