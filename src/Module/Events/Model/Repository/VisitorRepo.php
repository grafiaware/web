<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\Visitor;
use Events\Model\Entity\VisitorInterface;

use Events\Model\Dao\VisitorDao;

/**
 * Description of EventTypeTypeRepo
 *
 * @author pes2704
 */
class VisitorRepo extends RepoAbstract implements VisitorRepoInterface {

    protected $dao;

    public function __construct(VisitorDao $visitorDao, HydratorInterface $visitorHydrator) {
        $this->dao = $visitorDao;
        $this->registerHydrator($visitorHydrator);
    }

    /**
     *
     * @param type $id
     * @return VisitorInterface|null
     */
    public function get($id): ?VisitorInterface {
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

    public function add(VisitorInterface $event) {
        $this->addEntity($event);
    }

    public function remove(VisitorInterface $event) {
        $this->removeEntity($event);
    }

    protected function createEntity() {
        return new Visitor();
    }

    protected function indexFromEntity(VisitorInterface $event) {
        return $event->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
