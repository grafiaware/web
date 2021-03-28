<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\EventPresentation;
use Events\Model\Entity\EventPresentationInterface;

use Events\Model\Dao\EventPresentationDao;

/**
 * Description of EventTypeTypeRepo
 *
 * @author pes2704
 */
class EventPresentationRepo extends RepoAbstract implements EventPresentationRepoInterface {

    protected $dao;

    public function __construct(EventPresentationDao $eventContentTypeDao, HydratorInterface $eventContentTypeHydrator) {
        $this->dao = $eventContentTypeDao;
        $this->registerHydrator($eventContentTypeHydrator);
    }

    /**
     *d
     * @param string $id
     * @return EventPresentationInterface|null
     */
    public function get($id): ?EventPresentationInterface {
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

    public function add(EventPresentationInterface $eventContentType) {
        $this->addEntity($eventContentType);
    }

    public function remove(EventPresentationInterface $eventContentType) {
        $this->removeEntity($eventContentType);
    }

    protected function createEntity() {
        return new EventPresentation();
    }

    protected function indexFromEntity(EventPresentationInterface $eventContentType) {
        return $eventContentType->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
