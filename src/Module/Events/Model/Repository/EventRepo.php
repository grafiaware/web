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

/**
 * Description of MenuItemRepo
 *
 * @author pes2704
 */
class EventRepo extends RepoAbstract implements EventRepoInterface {

    protected $dao;

    public function __construct(EventDao $eventDao, HydratorInterface $eventHydrator) {
        $this->dao = $eventDao;
        $this->registerHydrator($eventHydrator);
    }

    /**
     *
     * @param type $id
     * @return LoginInterface|null
     */
    public function get($id): ?EventInterface {
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

    public function add(EventInterface $event) {
        $this->addEntity($event);
    }

    public function remove(EventInterface $event) {
        $this->removeEntity($event);
    }

    protected function createEntity() {
        return new Event();
    }

    protected function indexFromEntity(EventInterface $event) {
        return $event->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
