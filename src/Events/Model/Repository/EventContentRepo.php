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
 * Description of EventTypeTypeRepo
 *
 * @author pes2704
 */
class EventContentRepo extends RepoAbstract implements EventContentRepoInterface {

    protected $dao;

    public function __construct(EventContentDao $eventContentTypeDao, HydratorInterface $eventContentTypeHydrator) {
        $this->dao = $eventContentTypeDao;
        $this->registerHydrator($eventContentTypeHydrator);
    }

    /**
     *d
     * @param string $id
     * @return EventContentInterface|null
     */
    public function get($id): ?EventContentInterface {
        $index = $this->indexFromKey($id);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($id));
        }
        return $this->collection[$index] ?? null;
    }

    public function findAll() {
        $selected = [];
        foreach ($this->dao->findAll() as $eventContentRow) {
            $index = $this->indexFromRow($eventContentRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $eventContentRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function add(EventContentInterface $eventContentType) {
        $this->addEntity($eventContentType);
    }

    public function remove(EventContentInterface $eventContentType) {
        $this->removeEntity($eventContentType);
    }

    protected function createEntity() {
        return new EventContent();
    }

    protected function indexFromEntity(EventContentInterface $eventContentType) {
        return $eventContentType->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
