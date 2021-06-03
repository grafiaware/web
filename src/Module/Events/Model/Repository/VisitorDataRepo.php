<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\VisitorDataInterface;
use Events\Model\Entity\VisitorData;
use Events\Model\Dao\VisitorDataDao;
use Events\Model\Hydrator\VisitorDataHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class VisitorDataRepo extends RepoAbstract implements VisitorDataRepoInterface {

    public function __construct(VisitorDataDao $visitorDataDao, VisitorDataHydrator $visitorDataHydrator) {
        $this->dao = $visitorDataDao;
        $this->registerHydrator($visitorDataHydrator);
    }

    /**
     *
     * @param type $id
     * @return VisitorDataInterface|null
     */
    public function get($id): ?VisitorDataInterface {
        $index = $id;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($id));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        $selected = [];
        foreach ($this->dao->find($whereClause, $touplesToBind) as $enrollRow) {
            $index = $this->indexFromRow($enrollRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $enrollRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function findAll() {
        $selected = [];
        foreach ($this->dao->findAll() as $enrollRow) {
            $index = $this->indexFromRow($enrollRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $enrollRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function add(VisitorDataInterface $visitorData) {
        $this->addEntity($visitorData);
    }

    public function remove(VisitorDataInterface $visitorData) {
        $this->removeEntity($visitorData);
    }

    protected function createEntity() {
        return new VisitorData();
    }

    protected function indexFromEntity(VisitorDataInterface $visitorData) {
        return $visitorData->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }


}
