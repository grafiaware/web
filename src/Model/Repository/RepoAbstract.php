<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\DaoInterface;
use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;

use Model\Repository\Association\AssociationFactoryInterface;
use Model\Repository\Association\AssociationOneToOneFactory;
use Model\Repository\Association\AssociationOneToManyFactory;

/**
 * Description of RepoAbstract
 *
 * @author pes2704
 */
abstract class RepoAbstract implements RepoInterface {

    protected $collection = [];
    protected $removed = [];

    private $associations = [];

    private $hydrators = [];

    /**
     * @var DaoInterface
     */
    protected $dao;

    /**
     * @var HydratorInterface array of
     */
    protected $hydrator;

    /**
     *
     * @param type $parentPropertyName
     * @param type $parentIdName
     * @param \Model\Repository\RepoAssotiatedOneInterface $repo
     */
    protected function registerOneToOneAssotiation($parentPropertyName, $parentIdName, RepoAssotiatedOneInterface $repo) {
        $this->associations[$parentPropertyName] = new AssociationOneToOneFactory($parentPropertyName, $parentIdName, $repo);
    }

    protected function registerOneToManyAssotiation($parentPropertyName, $parentIdName, RepoAssotiatedManyInterface $repo) {
        $this->associations[$parentPropertyName] = new AssociationOneToManyFactory($parentPropertyName, $parentIdName, $repo);
    }

    protected function addCreatedAssociations(&$row): void {
        foreach ($this->associations as $association) {
            /** @var AssociationFactoryInterface $association */
            $association->createAssociated($row);
        }
    }

    protected function registerHydrator(HydratorInterface $hydrator) {
        $this->hydrators[] = $hydrator;
    }

    protected function hydrate(EntityInterface $entity, &$row) {
        /** @var HydratorInterface $hydrator */
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($entity, $row);
        }
    }

    protected function extract(EntityInterface $entity, &$row) {
        /** @var HydratorInterface $hydrator */
        foreach ($this->hydrators as $hydrator) {
            $hydrator->extract($entity, $row);
        }
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    protected function recreateEntity($index, $row) {
        if ($row) {
            $this->addCreatedAssociations($row);
            $entity = $this->createEntity();  // definována v konkrétní třídě - adept na entity managera
            $this->hydrate($entity, $row);
            $entity->setPersisted();
            $this->collection[$index] = $entity;
        }
    }

    public function flush() {
        if ( !($this instanceof RepoReadonlyInterface)) {
            /** @var \Model\Entity\EntityAbstract $entity */
            foreach ($this->collection as $entity) {
                $row = [];
                $this->extract($entity, $row);
                if ($entity->isPersisted()) {
                    $this->dao->update($row);
                } else {
                    $this->dao->insert($row);
                    $entity->setPersisted();
                }
            }
            $this->collection = [];
            foreach ($this->removed as $entity) {
                $row = [];
                $this->extract($entity, $row);
                $this->dao->delete($row);
                $entity->setUnpersisted();
            }
            $this->removed = [];
        }
    }

    public function __destruct() {
        $this->flush();
    }


}
