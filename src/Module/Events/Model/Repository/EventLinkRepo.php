<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\EventLink;
use Events\Model\Entity\EventLinkInterface;

use Events\Model\Dao\EventLinkDao;

/**
 * Description of EventLinkRepo
 *
 * @author pes2704
 */
class EventLinkRepo extends RepoAbstract implements EventLinkRepoInterface {

    protected $dao;

    public function __construct(EventLinkDao $eventLinkDao, HydratorInterface $eventLinkHydrator) {
        $this->dataManager = $eventLinkDao;
        $this->registerHydrator($eventLinkHydrator);
    }

    /**
     *
     * @param string $id
     * @return EventLinkInterface|null
     */
    public function get($id): ?EventLinkInterface {
        $key = $this->dataManager->getForeignKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }

    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    public function findAll(): array {
        return $this->findEntities();
    }

    public function add(EventLinkInterface $eventLink) {
        $this->addEntity($eventLink);
    }

    public function remove(EventLinkInterface $eventLink) {
        $this->removeEntity($eventLink);
    }

    protected function createEntity() : EventLinkInterface {
        return new EventLink();
    }

    protected function indexFromEntity(EventLinkInterface $eventLink) {
        return $eventLink->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
