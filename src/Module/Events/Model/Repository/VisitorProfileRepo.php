<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Entity\VisitorProfile;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Hydrator\VisitorProfileHydrator;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class VisitorProfileRepo extends RepoAbstract implements VisitorProfileRepoInterface {

    public function __construct(VisitorProfileDao $visitorDataDao, VisitorProfileHydrator $visitorDataHydrator) {
        $this->dataManager = $visitorDataDao;
        $this->registerHydrator($visitorDataHydrator);
    }

    /**
     *
     * @param type $loginName
     * @return VisitorProfileInterface|null
     */
    public function get($loginName): ?VisitorProfileInterface {
        return $this->getEntity($loginName);
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    public function findAll() {
        return $this->findEntities();
    }

    public function add(VisitorProfileInterface $visitorData) {
        $this->addEntity($visitorData);
    }

    public function remove(VisitorProfileInterface $visitorData) {
        $this->removeEntity($visitorData);
    }

    protected function createEntity() {
        return new VisitorProfile();
    }

    protected function indexFromKeyParams($loginName) {
        return $loginName;
    }

    protected function indexFromEntity(VisitorProfileInterface $visitorData) {
        return $visitorData->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }


}
