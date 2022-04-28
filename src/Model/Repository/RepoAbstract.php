<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\DaoEditInterface;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\DaoAutoincrementKeyInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Model\RowData\PdoRowData;
use Model\DataManager\DataManagerAbstract;

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

    private $collection = [];
    private $new = [];
    private $removed = [];
    private $data = [];
    private $refIndexToIndex = [];


    private $flushed = false;

    /**
     *
     * @var []
     */
    private $associations = [];

    /**
     *
     * @var HydratorInterface[]
     */
    private $hydrators = [];

    /**
     * @var  DataManagerAbstract
     */
    protected $dataManager;

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

    protected function getPrimaryKeyTouples(array $row): array {
        $keyAttribute = $this->dataManager->getPrimaryKeyAttribute();
        $key = [];
        foreach ($keyAttribute as $field) {
            if( ! array_key_exists($field, $row)) {
                throw new UnableRecreateEntityException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Atribut klíče obsahuje pole '$field' a pole dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            if (is_scalar($row[$field])) {
                $key[$field] = $row[$field];
            } else {
                $t = gettype($row[$field]);
                throw new UnableRecreateEntityException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Zadaný atribut klíče obsahuje v položce '$field' neskalární hodnotu. Hodnoty v položce '$field' je typu '$t'.");
            }
        }
        return $key;
    }

    /**
     *
     * @param variadic $id
     * @return EntityInterface|null
     */
    protected function getEntity(array $id) {
        $index = $this->indexFromRow($id);
        if (!isset($this->collection[$index]) AND !isset($this->removed[$index])) {
            $this->recreateEntity($index, $this->recreateData($index, $id));
        }
        return $this->collection[$index] ?? NULL;
    }

    /**
     *
     * @param variadic $referenceId
     * @return EntityInterface|null
     */
    protected function getEntityByReference(array $referenceId): ?EntityInterface {
        // vždy čte data - neví jestli jsou v $this->data
        $rowData = $this->dataManager->getByFk($referenceId);
        return $this->addEntityByRowData($rowData);
    }

    protected function findEntities($whereClause="", $touplesToBind=[]) {
        return $this->addEntitiesByRowDataArray($this->dataManager->find($whereClause, $touplesToBind));
    }

    protected function findEntitiesByReference(array $referenceId) {
        return $this->addEntitiesByRowDataArray($this->dataManager->findByFk(...$referenceId));
    }

    /**
     * Přidá entity na základě pole Rowdata objektů.
     *
     * Metoda je vhodná pro případy, kdy jsou načtena z úložiště současně data pro rodičovskou i potomkovskou entitu
     * - například pomocí SELECT FROM table1 JOIN table2
     *
     * @param type $rowDataArray
     * @return array
     */
    protected function addEntitiesByRowDataArray($rowDataArray): array {
        $selected = [];
        foreach ($rowDataArray as $rowData) {
            $added = $this->addEntityByRowData($rowData);  // může vracet null
            if (isset($added)) {
                $selected[] = $added;
            }
        }
        return $selected;
    }

    /**
     * Přidá entitu na základě Rowdata objektu.
     *
     * Metoda je vhodná pro případy, kdy jsou načtena z úložiště současně data pro rodičovskou i potomkovskou entitu
     * - například pomocí SELECT FROM table1 JOIN table2
     *
     * @param type $rowData
     * @return type
     */
    protected function addEntityByRowData($rowData=null) {
        if (!isset($rowData)) {
            return null;
        }
        $index = $this->indexFromRow($rowData);
        if (!isset($this->collection[$index]) AND !isset($this->removed[$index])) {
            $this->addData($index, $rowData);  // natvrdo dá rowData do $this->data
            $this->recreateEntity($index, $rowData);
        }
        return $this->collection[$index] ?? null;
    }



    private function recreateData($index, array $id) {
        $rowData = $this->dataManager->get($id);
        if(isset($rowData)) {
            $this->addData($index, $rowData);
        }
        return $rowData ?? null;
    }

    private function addData($index, RowDataInterface $rowData = null) {
        $this->data[$index] = $rowData;
    }

    /**
     *
     * @param string $index
     * @return string|null
     * @throws UnableRecreateEntityException
     */
    private function recreateEntity($index, RowDataInterface $rowData = null) {
        if(isset($rowData)) {
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
        }
    }

    private function recreateAssociations(RowDataInterface $rowData): void {
        foreach ($this->associations as $interfaceName => $association) {
            if ($association instanceof AssociationOneToManyInterface) {
                $rowData->forcedSet($interfaceName, $association->getAllAssociatedEntities($rowData));
            } elseif($association instanceof AssociationOneToOneInterface) {
                $rowData->forcedSet($interfaceName, $association->getAssociatedEntity($rowData));
            } else {
                throw new \LogicException("Neznámý typ asociace pro $interfaceName");
            }
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
            if ($this->dataManager instanceof DaoAutoincrementKeyInterface) {
                $rowData = $this->createRowData();
                $this->extract($entity, $rowData);
                $this->dataManager->insert($rowData);
                $entity->setPersisted();
                $this->dataManager->setAutoincrementedValue($rowData);
                $this->hydrate($entity, $rowData);  //získá hodnotu klíče
                $index = $this->indexFromEntity($entity);  // z hodnoty klíče
                $this->collection[$index] = $entity;
                $this->addData($index, $rowData);  // natvrdo dá rowData do $this->data
            } elseif ($this->dataManager instanceof DaoKeyDbVerifiedInterface ) {
                $rowData = $this->createRowData();
                $this->extract($entity, $rowData);
                try {
                    $this->dataManager->insert($rowData);
                    $entity->setPersisted();
                    $index = $this->indexFromEntity($entity);
                    $this->collection[$index] = $entity;
                    $this->addData($index, $rowData);  // natvrdo dá rowData do $this->data
                } catch ( DaoKeyVerificationFailedException $verificationFailedExc) {
                    throw new UnableAddEntityException('Entita má nastavenu hodnotou klíče, která již v databázi existuje, nelze zapsat do databáze.', 0, $verificationFailedExc);
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
    private function addAssociated($row) {
//    private function addAssociated($row, EntityInterface $entity) {
        foreach ($this->associations as $interfaceName => $association) {
            if (isset($row[$interfaceName])) {
                foreach ($row[$interfaceName] as $assocEntity) {  // asociovaná entita nemusí existovat - agregát je i tak validní
                    if (!$assocEntity->isPersisted()) {
                        $association->addAssociatedEntity($assocEntity);  // child repo add
                    }
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
        } else {   // smazání nepersistované entity před uložením do db
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
    protected function removeAssociated($row) {
//    protected function removeAssociated($row, EntityInterface $entity) {
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
                    $rowData = $this->createRowData();
                    $this->extract($entity, $rowData);
                    $this->dataManager->insert($rowData);
                    $this->addAssociated($rowData);
                    $entity->setPersisted();
                    $entity->unlock();
                    $this->new = []; // při dalším pokusu o find se bude volat recteateEntity, entita se zpětně načte z db (včetně případného autoincrement id a dalších generovaných sloupců)
                }
                $this->flushChildRepos();  //pokud je vnořená agregovaná entita - musí se provést její insert
            }

            foreach ($this->collection as $index => $entity) {
                if (!$entity->isPersisted()) {
                    throw new \LogicException("V collection je nepersistovaná entita.");
                }
                $rowData = $this->data[$index];
                $this->extract($entity, $rowData);
//                $this->addAssociated($rowData, $entity);
                $this->addAssociated($rowData);
            }
            $this->flushChildRepos();  //pokud je vnořená agregovaná entita přidána později - musí se provést její insert teď
            foreach ($this->collection as $index => $entity) {
                $rowData = $this->data[$index];
                if ($rowData->isChanged()) {
                    $this->dataManager->update($rowData);
                }
            }
            $this->collection = [];

            foreach ($this->removed as $index => $entity) {
                $rowData = $this->createRowData();
                $this->extract($entity, $rowData);
//                $this->removeAssociated($rowData, $entity);
                $this->removeAssociated($rowData);
            }
            $this->flushChildRepos();
            foreach ($this->removed as $index => $entity) {
                $this->dataManager->delete($this->data[$index]);
                $entity->setUnpersisted();
                $entity->unlock();
                unset($this->data[$index]);
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
