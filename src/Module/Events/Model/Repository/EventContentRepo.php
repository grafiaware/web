<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\EventContent;
use Events\Model\Entity\EventContentInterface;

use Events\Model\Dao\EventContentDao;

/**
 * Description of EventContentRepo
 *
 * @author pes2704
 */
class EventContentRepo extends RepoAbstract implements EventContentRepoInterface {

    protected $dao;

    public function __construct(EventContentDao $eventContentDao, HydratorInterface $eventContentHydrator) {
        $this->dataManager = $eventContentDao;
        $this->registerHydrator($eventContentHydrator);
    }

    /**
     *
     * @param string $id
     * @return EventContentInterface|null
     */
    public function get($id): ?EventContentInterface {
        $key = $this->dataManager->getForeignKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }

    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    public function findAll(): array {
        return $this->findEntities();
    }

    public function add(EventContentInterface $eventContent) {
        $this->addEntity($eventContent);
    }

    public function remove(EventContentInterface $eventContent) {
        $this->removeEntity($eventContent);
    }

    protected function createEntity() : EventContentInterface {
        return new EventContent();
    }

    protected function indexFromEntity(EventContentInterface $eventLink) {
        return $eventLink->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}