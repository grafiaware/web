<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\EntityInterface;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Entity\Document;
use Events\Model\Dao\DocumentDao;
use Events\Model\Hydrator\DocumentHydrator;

//use Model\Repository\Exception\UnableRecreateEntityException;

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
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return DocumentInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     * 
     * @return DocumentInterface[]
     */
    public function findAll() : array  {
        return $this->findEntities();
    }
    
    /**
     * 
     * @param DocumentInterface $document
     * @return void
     */
    public function add(DocumentInterface $document) : void {
        $this->addEntity($document);
    }
    
    /**
     * 
     * @param DocumentInterface $document
     * @return void
     */
    public function remove(DocumentInterface $document) : void {
        $this->removeEntity($document);
    }



    protected function createEntity() {
        return new Document();
    }

    protected function indexFromEntity(DocumentInterface $document) {
        return $document->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
