<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\EventContentType;
use Events\Model\Entity\EventContentTypeInterface;

use Events\Model\Dao\EventContentTypeDao;

/**
 * Description of EventTypeTypeRepo
 *
 * @author pes2704
 */
class EventContentTypeRepo extends RepoAbstract implements EventTypeRepoInterface {

    protected $dao;

    public function __construct(EventContentTypeDao $eventContentTypeDao, HydratorInterface $eventContentTypeHydrator) {
        $this->dao = $eventContentTypeDao;
        $this->registerHydrator($eventContentTypeHydrator);
    }

    /**
     *d
     * @param string $type
     * @return EventContentTypeInterface|null
     */
    public function get($type): ?EventContentTypeInterface {
        $index = $this->indexFromKey($type);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($type));
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

    public function add(EventContentTypeInterface $eventContentType) {
        $this->addEntity($eventContentType);
    }

    public function remove(EventContentTypeInterface $eventContentType) {
        $this->removeEntity($eventContentType);
    }

    protected function createEntity() {
        return new EventContentType();
    }

    protected function indexFromEntity(EventContentTypeInterface $eventContentType) {
        return $eventContentType->getType();
    }

    protected function indexFromRow($row) {
        return $row['type'];
    }
}
