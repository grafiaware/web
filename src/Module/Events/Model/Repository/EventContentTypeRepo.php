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
use Events\Model\Repository\EventContentTypeRepoInterface;

/**
 * Description of EventTypeTypeRepo
 *
 * @author pes2704
 */
class EventContentTypeRepo extends RepoAbstract implements EventContentTypeRepoInterface {

    protected $dao;

    public function __construct(EventContentTypeDao $eventContentTypeDao, HydratorInterface $eventContentTypeHydrator) {
        $this->dataManager = $eventContentTypeDao;
        $this->registerHydrator($eventContentTypeHydrator);
    }

    /**
     *d
     * @param string $type
     * @return EventContentTypeInterface|null
     */
    public function get($type): ?EventContentTypeInterface {
        $key = $this->getPrimaryKeyTouples(['type'=>$type]);
        return $this->getEntity($key);    }

    public function findAll() {
        return $this->findEntities();
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
