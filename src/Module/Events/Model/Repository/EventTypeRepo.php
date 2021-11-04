<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\EventType;
use Events\Model\Entity\EventTypeInterface;

use Events\Model\Dao\EventTypeDao;

/**
 * Description of EventTypeTypeRepo
 *
 * @author pes2704
 */
class EventTypeRepo extends RepoAbstract implements EventTypeRepoInterface {

    protected $dao;

    public function __construct(EventTypeDao $eventTypeDao, HydratorInterface $eventTypeHydrator) {
        $this->dao = $eventTypeDao;
        $this->registerHydrator($eventTypeHydrator);
    }

    /**
     *
     * @param type $id
     * @return EventTypeInterface|null
     */
    public function get($id): ?EventTypeInterface {
        return $this->getEntity($id);
    }

    public function findAll() {
        return $this->findAllEntities();
    }

    public function add(EventTypeInterface $eventType) {
        $this->addEntity($eventType);
    }

    public function remove(EventTypeInterface $eventType) {
        $this->removeEntity($eventType);
    }

    protected function createEntity() {
        return new EventType();
    }

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(EventTypeInterface $eventType) {
        return $eventType->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
