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

use Model\Repository\Association\AssociationInterface;
use Model\Repository\Association\AssociationOneToOne;
use Model\Repository\Association\AssociationOneToMany;
use Model\Repository\RepoAssotiatedOneInterface;
use Model\Repository\RepoAssotiatedManyInterface;
use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;
use Model\Repository\Exception\UnableRecreateEntityException;
use Model\Repository\Exception\BadImplemntastionOfChildRepository;

/**
 * Description of RepoAbstract
 *
 * @author pes2704
 */
abstract class RepoAbstract {

    public static $counter;
    protected $count;
    protected $oid;

    protected $collection = [];
    protected $new = [];
    protected $removed = [];

    /**
     *
     * @var AssociationOneToOne []
     */
    private $associations = [];
    private $children = [];

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
     * @param string $entityInterfaceName Jméno interface asociované entity
     * @param array $parentReferenceKeyAttribute Atribut klíče, který je referencí na data rodiče v úložišti dat. V databázi jde o referenční cizí klíč.
     * @param \Model\Repository\RepoAssotiatedOneInterface $repo
     */
    protected function registerOneToOneAssociation($entityInterfaceName, $parentReferenceKeyAttribute, RepoAssotiatedOneInterface $repo) {
        $this->associations[$entityInterfaceName] = new AssociationOneToOne($parentReferenceKeyAttribute, $repo);
    }

    /**
     *
     * @param string $entityClassName Interface asociované entity
     * @param array $parentReferenceKeyAttribute Atribut klíče, který je referencí na data rodiče v úložišti dat. V databázi jde o referenční cizí klíč.
     * @param \Model\Repository\RepoAssotiatedOneInterface $repo
     */
    protected function registerOneToManyAssotiation($entityClassName, $parentReferenceKeyAttribute, RepoAssotiatedManyInterface $repo) {
        $this->associations[$entityClassName] = new AssociationOneToMany($parentReferenceKeyAttribute, $repo);
    }

    protected function addAssociationsToRow(&$row): void {
        foreach ($this->associations as $className => $association) {
            $row[$className] = $association->getAssociated($row);
        }
    }

    /**
     *
     * @param type $entityInterfaceName Entita nebo null. Asociovaná entita (vátaná pomocí cizího klíče) nemusí existovat.
     * @param type $entity
     */
    protected function addAssociated($entityInterfaceName, $entity = null) {
        if (isset($entity)) {
            $this->associations[$entityInterfaceName]->addAssociated($entity);
        }
    }

    protected function removeAssociated($entityInterfaceName, $entty) {
        $this->associations[$entityInterfaceName]->removeAssociated($entity);
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

    protected function createEntity() {
        throw new BadImplemntastionOfChildRepository("Child repository must implement method createEntity().");
    }

//    protected function indexFromKeyParams() {
//        throw new BadImplemntastionOfChildRepository("Child repository must implement method indexFromKeyParams().");
//    }
//
//    protected function indexFromEntity() {
//        throw new BadImplemntastionOfChildRepository("Child repository must implement method indexFromEntity().");
//    }
//
//    protected function indexFromRow() {
//        throw new BadImplemntastionOfChildRepository("Child repository must implement method indexFromRow().");
//    }

    /**
     *
     * @param array $row
     * @return string index
     */
    protected function recreateEntity($index, $row): ?string {
        if ($row) {
            try {
                $this->addAssociationsToRow($row);
            } catch (UnableToCreateAssotiatedChildEntity $unex) {
                throw new UnableRecreateEntityException("Nelze obnovit agregovanou (vnořenou) entitu v repository ". get_called_class()." s indexem $index.", 0, $unex);
            }
            $entity = $this->createEntity();  // definována v konkrétní třídě - adept na entity managera
            $this->hydrate($entity, $row);
            $entity->setPersisted();
            $this->collection[$index] = $entity;
        }
        return $index ?? null;
    }

    protected function getKey($row) {
        $keyAttribute = $this->getKeyAttribute();
        if (is_array($keyAttribute)) {
            foreach ($keyAttribute as $field) {
                if( ! array_key_exists($field, $row)) {
                    throw new UnableRecreateEntityException("Nelze vytvořit klíč entity. Atribut klíče obsahuje pole $field a pole řádku dat pro vytvoření entity neobsahuje prvek s takovým kménem.");
                }
                $key[$field] = $row[$field];
            }
        } else {
            $key = $row[$keyAttribute];
        }
        return $key;
    }

    protected function indexFromKey($key) {
        if (is_array($key)) {
            return implode(array_values($key));
        } else{
            return $key;
        }
    }

    protected function addEntity(EntityInterface $entity): void {
        if ($entity->isPersisted()) {
            $this->collection[$this->indexFromEntity()] = $entity;
        } else {
            $this->new[] = $entity;
        }
    }

    protected function removeEntity(EntityInterface $entity): void {
        if ($entity->isPersisted()) {
            $this->removed[$this->indexFromEntity()] = $entity;
            unset($this->collection[$index]);
        } else {   // smazání před uložením do db
            foreach ($this->new as $key => $new) {
                if ($new === $entity) {
                    unset($this->new[$key]);
                }
            }
        }
    }

    public function flush(): void {
        if ( !($this instanceof RepoReadonlyInterface)) {
            /** @var \Model\Entity\EntityAbstract $entity */
            foreach ($this->collection as $entity) {
                $row = [];
                $this->extract($entity, $row);
                if ($entity->isPersisted()) {
                    if ($row) {     // $row po extractu musí obsahovat nějaká data, která je možno updatovat - v extarctu musí být vynechány "readonly" sloupce
                        $this->dao->update($row);
                    }
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
