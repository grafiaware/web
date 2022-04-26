<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Entity\EntityInterface;

use Events\Model\Entity\VisitorDataPostInterface;
use Events\Model\Entity\VisitorDataPost;
use Events\Model\Dao\VisitorDataPostDao;
use Events\Model\Hydrator\VisitorDataPostHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class VisitorDataPostRepo extends RepoAbstract implements VisitorDataPostRepoInterface {

    public function __construct(VisitorDataPostDao $visitorDataPostDao, VisitorDataPostHydrator $visitorDataPostHydrator) {
        $this->dataManager = $visitorDataPostDao;
        $this->registerHydrator($visitorDataPostHydrator);
    }

    /**
     *
     * @param string $loginName
     * @param string $shortName
     * @param string $positionName
     * @return VisitorDataPostInterface|null
     */
    public function get($loginName, $shortName, $positionName): ?VisitorDataPostInterface {
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
        return $visitorDataPost->getLoginName().$visitorDataPost->getShortName().$visitorDataPost->getPositionName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'].$row['short_name'].$row['position_name'];
    }


}
