<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Entity\EntityInterface;

use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Hydrator\VisitorJobRequestHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class VisitorJobRequestRepo extends RepoAbstract implements VisitorJobRequestRepoInterface {

    public function __construct(VisitorJobRequestDao $visitorJobRequestDao, VisitorJobRequestHydrator $visitorDataPostHydrator) {
        $this->dataManager = $visitorJobRequestDao;
        $this->registerHydrator($visitorDataPostHydrator);
    }

    /**
     *
     * @param string $loginName
     * @param string $shortName
     * @param string $positionName
     * @return VisitorJobRequestInterface|null
     */
    public function get($loginName): ?VisitorJobRequestInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_login_name'=>$loginName]);
        return $this->getEntity($loginName, $shortName, $positionName);
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    public function findAll() {
        return $this->findEntities();
    }

    public function findAllForPosition($shortName, $positionName) {
        $whereClause = "`short_name` = :short_name AND `position_name` = :position_name";
        $touplesToBind = [':short_name' => $shortName, ':position_name' => $positionName];
        return $this->findEntities($whereClause, $touplesToBind);
    }

    public function add(VisitorJobRequestInterface $visitorDataPost) {
        $this->addEntity($visitorDataPost);
    }

    public function remove(VisitorJobRequestInterface $visitorDataPost) {
        $this->removeEntity($visitorDataPost);
    }

    protected function createEntity() {
        return new VisitorJobRequest();
    }

    protected function indexFromEntity(VisitorJobRequestInterface $visitorDataPost) {
        return $visitorDataPost->getLoginLoginName().$visitorDataPost->getJobId().$visitorDataPost->getPositionName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'].$row['short_name'].$row['position_name'];
    }


}
