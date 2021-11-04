<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\EntityInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\Paper;
use Red\Model\Dao\PaperDao;
use Model\Dao\DaoChildInterface;
use Red\Model\Hydrator\PaperHydrator;

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
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return PaperInterface|null
     */
    public function getByReference($menuItemIdFk): ?EntityInterface {
        return $this->getEntityByReference($menuItemIdFk);
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

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(PaperInterface $paper) {
        return $paper->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
