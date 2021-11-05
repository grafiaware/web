<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\DaoInterface;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\Exception\DaoKeyVerificationFailedException;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Model\RowData\PdoRowData;
use Model\DataManager\DataManager;

use Model\Repository\Association\AssociationOneToOne;
use Model\Repository\Association\AssociationOneToMany;
use Model\Repository\Association\AssociationOneToOneInterface;
use Model\Repository\Association\AssociationOneToManyInterface;
use Model\Repository\RepoAssotiatedOneInterface;
use Model\Repository\RepoAssotiatedManyInterface;
use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;
use Model\Repository\Exception\UnableRecreateEntityException;
use Model\Repository\Exception\BadImplemntastionOfChildRepository;

use Model\Repository\Exception\UnableAddEntityException;
use Model\Repository\Exception\OperationWithLockedEntityException;

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

    private $flushed = false;

    /**
     *
     * @var []
     */
    private $associations = [];

    private $hydrators = [];

    /**
     * @var  DataManager
     */
    protected $dataManager;

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
     * @param string $entityInterfaceName Jméno interface asociované entity
     * @param array $parentReferenceKeyAttribute Atribut klíče, který je referencí na data rodiče v úložišti dat. V databázi jde o referenční cizí klíč.
     * @param \Model\Repository\RepoAssotiatedOneInterface $repo
     */
    protected function registerOneToManyAssociation($entityInterfaceName, $parentReferenceKeyAttribute, RepoAssotiatedManyInterface $repo) {
        $this->associations[$entityInterfaceName] = new AssociationOneToMany($parentReferenceKeyAttribute, $repo);
    }

    protected function registerHydrator(HydratorInterface $hydrator) {
        $this->hydrators[] = $hydrator;
    }

    protected function hydrate(EntityInterface $entity, RowDataInterface $rowData) {
        /** @var HydratorInterface $hydrator */
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($entity, $rowData);
        }
    }

    protected function extract(EntityInterface $entity, RowDataInterface $rowData) {
        /** @var HydratorInterface $hydrator */
        foreach ($this->hydrators as $hydrator) {
            $hydrator->extract($entity, $rowData);
        }
    }

    protected function createEntity() {
        throw new BadImplemntastionOfChildRepository("Child repository must implement method createEntity().");
    }

    /**
     *
     * @param variadic $id
     * @return EntityInterface|null
     */
    protected function getEntity(...$id) {
        $index = $this->indexFromKeyParams(...$id);
        if (!isset($this->collection[$index])) {
            $rowData = $this->dataManager->get(...$id);
            if ($rowData) {
                $this->recreateEntity($index, $rowData);
            }
        }
        return $this->collection[$index] ?? NULL;
    }

    /**
     *
     * @param variadic $referenceId
     * @return EntityInterface|null
     */
    protected function getEntityByReference(...$referenceId): ?EntityInterface {
        $rowData = $this->dataManager->getByFk(...$referenceId);
        if (!$rowData) {
            return null;
        }
        $index = $this->indexFromRow($rowData);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $rowData);
        }
        return $this->collection[$index] ?? NULL;
    }

    protected function findEntities($whereClause=null, $touplesToBind=[]) {
        $selected = [];
        foreach ($this->dataManager->find($whereClause, $touplesToBind) as $rowData) {
            $index = $this->indexFromRow($rowData);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $rowData);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    /**
     *
     * @param string $index
     * @param RowDataInterface $rowData
     * @return string|null
     * @throws UnableRecreateEntityException
     */
    protected function recreateEntity($index, RowDataInterface $rowData): ?string {
        $entity = $this->createEntity();  // definována v konkrétní třídě - adept na entity managera
        try {
            $this->recreateAssociations($rowData);
        } catch (UnableToCreateAssotiatedChildEntity $unex) {
            throw new UnableRecreateEntityException("Nelze obnovit agregovanou (vnořenou) entitu v repository ". get_called_class()." s indexem $index.", 0, $unex);
        }
        $this->hydrate($entity, $rowData);
        $entity->setPersisted();
        $this->collection[$index] = $entity;
        $this->flushed = false;
        return $index ?? null;
    }

    protected function recreateAssociations(RowDataInterface $rowData): void {
        foreach ($this->associations as $interfaceName => $association) {
            if ($association instanceof AssociationOneToManyInterface) {
                $rowData[$interfaceName] = $association->getAllAssociatedEntities($rowData);
            } elseif($association instanceof AssociationOneToOneInterface) {
                $rowData[$interfaceName] = $association->getAssociatedEntity($rowData);
            } else {
                throw new \LogicException("Neznámý typ asociace pro $interfaceName");
            }
        }
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

    private function createRowData() {
        return new PdoRowData();
    }

    protected function addEntity(EntityInterface $entity): void {
        if ($entity->isLocked()) {
            throw new OperationWithLockedEntityException("Nelze přidávat přidanou nebo smazanou entitu.");
        }
        if ($entity->isPersisted()) {
            $this->collection[$this->indexFromEntity($entity)] = $entity;
        } else {
            if ( $this->dataManager instanceof DaoKeyDbVerifiedInterface ) {
                $row = $this->createRowData();
                $this->extract($entity, $row);
                try {
                    $this->dataManager->insertWithKeyVerification($row);
                    $entity->setPersisted();
                    $this->collection[$this->indexFromEntity($entity)] = $entity;
                } catch ( DaoKeyVerificationFailedException $verificationFailedExc) {
                    throw new UnableAddEntityException('Entitu s nastavenou hodnotou klíče nelze zapsat do databáze.', 0, $verificationFailedExc);
                }
            } else {
                $this->new[] = $entity;
                $entity->lock();
            }
        }
        $this->flushed = false;
    }

    /**
     *
     * @param type $entity Agregátní entita.
     */
    protected function addAssociated($row, EntityInterface $entity) {
        foreach ($this->associations as $interfaceName => $association) {
            foreach ($row[$interfaceName] as $assocEntity) {  // asociovaná entita nemusí existovat - agregát je i tak validní
                if (!$assocEntity->isPersisted()) {
                    $association->addAssociatedEntity($assocEntity);
                }
            }
        }
    }

    protected function removeEntity(EntityInterface $entity): void {
        if ($entity->isLocked()) {
            throw new OperationWithLockedEntityException("Nelze mazat přidanou nebo smazanou entitu.");
        }
        if ($entity->isPersisted()) {
            $index = $this->indexFromEntity($entity);
            $this->removed[$index] = $entity;
            unset($this->collection[$index]);
            $entity->setUnpersisted();
            $entity->lock();
        } else {   // smazání před uložením do db
            foreach ($this->new as $key => $new) {
                if ($new === $entity) {
                    unset($this->new[$key]);
                }
            }
        }
        $this->flushed = false;
    }

    /**
     *
     * @param string $entityInterfaceName
     * @param type $entity Entita nebo null. Asociovaná entita (vrácená pomocí cizího klíče) nemusí existovat.
     */
    protected function removeAssociated($row, EntityInterface $entity) {
        foreach ($this->associations as $interfaceName => $association) {
            if (isset($row[$interfaceName]) AND $row[$interfaceName]->isPersisted()) {  // asociovaná entita nemusí existovat - agregát je i tak validní
                $association->removeAssociatedEntity($row[$interfaceName]);
            }
        }
    }

    public function flush(): void {
        if ($this->flushed) {
            return;
        }
        if ( !($this instanceof RepoReadonlyInterface)) {
            /** @var \Model\Entity\EntityAbstract $entity */
            if ( ! ($this->dataManager instanceof DaoKeyDbVerifiedInterface)) {   // DaoKeyDbVerifiedInterface musí ukládat (insert) vždy již při nastavování hodnoty primárního klíče
                foreach ($this->new as $entity) {
                    $row = $this->createRowData();
                    $this->extract($entity, $row);
                    $this->dataManager->insert($row);
                    $this->addAssociated($row, $entity);
                    $this->flushChildRepos();  //pokud je vnořená agregovaná entita - musí se provést její insert
                    $entity->setPersisted();
                }
            }
            $this->new = []; // při dalším pokusu o find se bude volat recteateEntity, entita se zpětně načte z db (včetně případného autoincrement id a dalších generovaných sloupců)

            foreach ($this->collection as $entity) {
                $row = $this->createRowData();
                $this->extract($entity, $row);
                $this->addAssociated($row, $entity);
                $this->flushChildRepos();  //pokud je vnořená agregovaná entita přidána později - musí se provést její insert teď
                if ($entity->isPersisted()) {
                    if ($row) {     // $row po extractu musí obsahovat nějaká data, která je možno updatovat - v extractu musí být vynechány "readonly" sloupce
                        $this->dataManager->update($row);
                    }
                } else {
                    throw new \LogicException("V collection je nepersistovaná entita.");
                }
            }
            $this->collection = [];

            foreach ($this->removed as $entity) {
                $row = $this->createRowData();
                $this->extract($entity, $row);
                $this->removeAssociated($row, $entity);
                $this->flushChildRepos();
                $this->dataManager->delete($row);
                $entity->setUnpersisted();
            }
            $this->removed = [];

        } else {
            if ($this->new OR $this->removed) {
                throw new \LogicException("Repo je read only a byly do něj přidány nebo z něj smazány entity.");
            }
        }
        $this->flushed = true;
    }

    private function flushChildRepos() {
        foreach ($this->associations as $association) {
            $association->flushChildRepo();
        }
    }

    public function __destruct() {
        $this->flush();
    }


}
