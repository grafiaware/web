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
        $index = $this->indexFromKey($id);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($id));
        }
        return $this->collection[$index] ?? null;
    }

    public function findAll() {
        $selected = [];
        foreach ($this->dao->findAll() as $eventRow) {
            $index = $this->indexFromRow($eventRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $eventRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function add(EventTypeInterface $event) {
        $this->addEntity($event);
    }

    public function remove(EventTypeInterface $event) {
        $this->removeEntity($event);
    }

    protected function createEntity() {
        return new EventType();
    }

    protected function indexFromEntity(EventTypeInterface $event) {
        return $event->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
