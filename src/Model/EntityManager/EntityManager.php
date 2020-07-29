<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\EntityManager;

use Model\Entity\EntityInterface;

/**
 * Description of EntityManager
 *
 * @author pes2704
 */
class EntityManager implements EntityManagerInterface {

    protected $collection = [];
    protected $new = [];
    protected $removed = [];

    private $hydrators = [];


    protected function registerHydrator($entityClass, HydratorInterface $hydrator) {
        $this->hydrators[$entityClass][] = $hydrator;
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    public function recreateEntity($entityClass, $index, $row): ?EntityInterface {
        if (!isset($this->collection[$index])) {
            if ($row) {
                $this->addCreatedAssociations($row);
                $entity = new $entityClass();  // definována v konkrétní třídě - adept na entity managera
                $this->hydrate($entityClass, $entity, $row);
                $entity->setPersisted();
                $this->collection[$index] = $entity;
            }
        }
        return $this->collection[$index] ?? NULL;
    }

    public function hydrate($entityClass, EntityInterface $entity, &$row) {
        /** @var HydratorInterface $hydrator */
        foreach ($this->hydrators[$entityClass] as $hydrator) {
            $hydrator->hydrate($entity, $row);
        }
    }

    public function extract($entityClass, EntityInterface $entity, &$row) {
        /** @var HydratorInterface $hydrator */
        foreach ($this->hydrators[$entityClass] as $hydrator) {
            $hydrator->extract($entity, $row);
        }
    }

    public function flush() {
        if ( !($this instanceof RepoReadonlyInterface)) {
            /** @var \Model\Entity\EntityAbstract $entity */
            foreach ($this->collection as $entityClass => $entities) {
                foreach ($entities as $entity) {
                    $row = [];
                    $this->extract($entityClass, $entity, $row);
                    if ($entity->isPersisted()) {
                        $this->dao->update($row);
                    } else {
                        throw new \LogicException("V collection $entityClass je nepersistovaná entita.");
                    }
                }
            }

            foreach ($this->collection as $entityClass => $entities) {
                foreach ($entities as $entity) {
                    $row = [];
                    $this->extract($entityClass, $entity, $row);
                    $this->dao->insert($row);
                }
            }
            $this->new = []; // při dalším pokusu o find se bude volat recteateEntity, entita se zpětně načte z db (včetně případného autoincrement id a dalších generovaných sloupců)
            foreach ($this->collection as $entityClass => $entities) {
                foreach ($entities as $entity) {
                    $row = [];
                    $this->extract($entityClass, $entity, $row);
                    $this->dao->delete($row);
                    $entity->setUnpersisted();
                }
            }
            $this->removed = [];
        } else {
            if ($this->new OR $this->removed) {
                throw new \LogicException("Repo je read only a byly do něj přidány nebo z něj smazány entity.");
            }
        }
    }

    public function __destruct() {
        $this->flush();
    }
}
