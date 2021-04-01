<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\EntityInterface;
use Model\Entity\VisitorDataPostInterface;
use Model\Entity\VisitorDataPost;
use Model\Dao\VisitorDataPostDao;
use Model\Hydrator\VisitorDataPostHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class VisitorDataPostRepo extends RepoAbstract implements VisitorDataPostRepoInterface {

    public function __construct(VisitorDataPostDao $visitorDataPostDao, VisitorDataPostHydrator $visitorDataPostHydrator) {
        $this->dao = $visitorDataPostDao;
        $this->registerHydrator($visitorDataPostHydrator);
    }

    /**
     *
     * @param type $loginName, $shortName
     * @return VisitorDataPostInterface|null
     */
    public function get($loginName, $shortName): ?VisitorDataPostInterface {
        $index = $loginName.$shortName;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($loginName, $shortName));
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

    public function add(VisitorDataPostInterface $visitorDataPost) {
        $this->addEntity($visitorDataPost);
    }

    public function remove(VisitorDataPostInterface $visitorDataPost) {
        $this->removeEntity($visitorDataPost);
    }

    protected function createEntity() {
        return new VisitorDataPost();
    }

    protected function indexFromEntity(VisitorDataPostInterface $visitorDataPost) {
        return $visitorDataPost->getLoginName().$visitorDataPost->getShortName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'].$row['short_name'];
    }


}
