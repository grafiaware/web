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

use Model\Repository\RepoPublishedOnlyModeInterface;

use Model\Repository\Association\AssociationFactoryInterface;
use Model\Repository\Association\AssociationOneToOneFactory;
use Model\Repository\Association\AssociationOneToManyFactory;
use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;
use Model\Repository\Exception\UnableRecreateEntityException;
/**
 * Description of RepoAbstract
 *
 * @author pes2704
 */
abstract class RepoAbstract implements RepoInterface {

    public static $counter;
    protected $count;
    protected $oid;

    protected $collection = [];
    protected $new = [];
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
    protected function recreateEntity($index, $row): void {
        if ($row) {
            try {
                $this->addCreatedAssociations($row);
            } catch (UnableToCreateAssotiatedChildEntity $unex) {
                throw new UnableRecreateEntityException("Nelze obnovit agregovanou entitu v repository ". get_called_class()." s indexem $index.", 0, $unex);
            }
            $entity = $this->createEntity();  // definována v konkrétní třídě - adept na entity managera
            $this->hydrate($entity, $row);
            $entity->setPersisted();
            $this->collection[$index] = $entity;
        }
    }

    protected function addEntity(EntityInterface $entity, $index=null): void {
        if ($index) {
            $this->collection[$index] = $entity;
        } else {
            $this->new[] = $entity;
        }
    }

    protected function removeEntity(EntityInterface $entity, $index=null): void {
        if ($index) {
            $this->removed[] = $entity;
        } else {   // smazání před uložením do db
            foreach ($this->new as $key => $new) {
                if ($new === $entity) {
                    unset($this->new[$key]);
                }
            }
        }
        unset($this->collection[$index]);
    }

    public function flush(): void {
        if ( !($this instanceof RepoReadonlyInterface)) {
            /** @var \Model\Entity\EntityAbstract $entity */
            foreach ($this->collection as $entity) {
                $row = [];
                $this->extract($entity, $row);
                if ($entity->isPersisted()) {
                    $this->dao->update($row);
                } else {
                    throw new \LogicException("V collection je nepersistovaná entita.");
                }
            }
            foreach ($this->new as $entity) {
                $row = [];
                $this->extract($entity, $row);
                $this->dao->insert($row);
            }
            $this->new = []; // při dalším pokusu o find se bude volat recteateEntity, entita se zpětně načte z db (včetně případného autoincrement id a dalších generovaných sloupců)
            foreach ($this->removed as $entity) {
                $row = [];
                $this->extract($entity, $row);
                $this->dao->delete($row);
                $entity->setUnpersisted();
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
