<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Dao\InstitutionTypeDao;
use Events\Model\Hydrator\InstitutionTypeHydrator;
use Events\Model\Repository\InstitutionTypeRepoInterface;
use Events\Model\Entity\InstitutionType;
use Events\Model\Entity\InstitutionTypeInterface;


/**
 * Description of InstitutionTypeRepo
 *
 * @author vlse2610
 */
class InstitutionTypeRepo extends RepoAbstract implements InstitutionTypeRepoInterface {    

    public function __construct( InstitutionTypeDao $institutionTypeDao, InstitutionTypeHydrator $institutionTypeHydrator) {
        $this->dataManager = $institutionTypeDao;
        $this->registerHydrator($institutionTypeHydrator);
    }
                

    /**
    * 
    * @param type $id
    * @return InstitutionTypeInterface|null
    */  
    public function get($id): ?InstitutionTypeInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }
    
    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return InstitutionTypeInterface[]
     */    
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
}
    
    
     /**
     * 
     * @return InstitutionTypeInterface[]
     */
    public function findAll() : array  {
       return $this->findEntities();
    }
    
    
    /**
     * 
     * @param InstitutionTypeInterface $institutionType
     * @return void
     */
    public function add(InstitutionTypeInterface $institutionType) :void {
        $this->addEntity($institutionType);
    }
    
    
    /**
     * 
     * @param InstitutionTypeInterface $institutionType
     * @return void
     */
    public function remove(InstitutionTypeInterface $institutionType) : void {
        $this->removeEntity($institutionType);
    }
 
        
    

    protected function createEntity() {
        return new InstitutionType();
    }

    protected function indexFromEntity(InstitutionTypeInterface $institutionType) {
        return $institutionType->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
