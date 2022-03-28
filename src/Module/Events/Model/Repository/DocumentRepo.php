<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\EntityInterface;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Entity\Document;
use Events\Model\Dao\DocumentDao;
use Events\Model\Hydrator\DocumentHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class DocumentRepo extends RepoAbstract implements DocumentRepoInterface {

    public function __construct(DocumentDao $documentDao, DocumentHydrator $documentHydrator) {
        $this->dataManager = $documentDao;
        $this->registerHydrator($documentHydrator);
    }

    /**
     *
     * @param type $id
     * @return DocumentInterface|null
     */
    public function get($id): ?DocumentInterface {
        return $this->getEntity($id);
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        return $this->findEntities($whereClause, $touplesToBind);
    }
    
    public function findAll() {
        return $this->findEntities();
    }

    public function add(DocumentInterface $document) {
        $this->addEntity($document);
    }

    public function remove(DocumentInterface $document) {
        $this->removeEntity($document);
    }

    protected function createEntity() {
        return new Document();
    }

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(DocumentInterface $document) {
        return $document->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
