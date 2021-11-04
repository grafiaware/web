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
        return $this->getEntity($type);
    }

    public function findAll() {
        return $this->findAllEntities();
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

    protected function indexFromKeyParams($type) {
        return $type;
    }

    protected function indexFromEntity(EventContentTypeInterface $eventContentType) {
        return $eventContentType->getType();
    }

    protected function indexFromRow($row) {
        return $row['type'];
    }
}
