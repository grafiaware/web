<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\Event;
use Events\Model\Entity\EventInterface;

use Events\Model\Dao\EventDao;
use Events\Model\Repository\EventRepoInterface;

/**
 * Description of MenuItemRepo
 *
 * @author pes2704
 */
class EventRepo extends RepoAbstract implements EventRepoInterface {

    protected $dao;

    public function __construct(EventDao $eventDao, HydratorInterface $eventHydrator) {
        $this->dataManager = $eventDao;
        $this->registerHydrator($eventHydrator);
    }

    /**
     *
     * @param type $id
     * @return EventInterface|null
     */
    public function get($id): ?EventInterface {
        return $this->getEntity($id);
    }

    public function findAll(): array{
        return $this->findEntities();
    }

    public function add(EventInterface $event) {
        $this->addEntity($event);
    }

    public function remove(EventInterface $event) {
        $this->removeEntity($event);
    }

    protected function createEntity() {
        return new Event();
    }

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(EventInterface $event) {
        return $event->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
