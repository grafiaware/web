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
        $this->dataManager = $eventContentTypeDao;
        $this->registerHydrator($eventContentTypeHydrator);
    }

    /**
     *d
     * @param string $id
     * @return EventContentInterface|null
     */
    public function get($id): ?EventContentInterface {
        return $this->getEntity($id);
    }

    public function findAll() {
        return $this->findAllEntities();
    }

    public function add(EventContentInterface $eventContentType) {
        $this->addEntity($eventContentType);
    }

    public function remove(EventContentInterface $eventContent) {
        $this->removeEntity($eventContent);
    }

    protected function createEntity() {
        return new EventContent();
    }

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(EventContentInterface $eventContent) {
        return $eventContent->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
