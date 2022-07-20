<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Repository\InstitutionRepoInterface;
use Events\Model\Entity\InstitutionInterface;
use Events\Model\Entity\Institution;
use Events\Model\Dao\InstitutionDao;
use Events\Model\Hydrator\InstitutionHydrator;

/**
 * Description of InstitutionRepo
 *
 * @author vlse2610
 */
class InstitutionRepo extends RepoAbstract implements InstitutionRepoInterface {    

    public function __construct( InstitutionDao $institutionDao, InstitutionHydrator $institutionHydrator) {
        $this->dataManager = $institutionDao;
        $this->registerHydrator($institutionHydrator);
    }    


   /**
    * 
    * @param type $id
    * @return InstitutionInterface|null
    */      
    public function get($id): ?InstitutionInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }
    
    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return InstitutionInterface[]
     */    
    public function find($whereClause=null, $touplesToBind=[]) : array {
        return $this->findEntities($whereClause, $touplesToBind);
    }
        
    
     /**
     * 
     * @return InstitutionInterface[]
     */
    public function findAll() : array {
        return $this->findEntities();
    }
    
    
    /**
     * 
     * @param InstitutionInterface $institution
     * @return void
     */
    public function add(InstitutionInterface $institution) :void {
        $this->addEntity($institution);
    }
    
    
    /**
     * 
     * @param InstitutionInterface $institution
     * @return void
     */
    public function remove(InstitutionInterface $institution) : void {
        $this->removeEntity($institution);
    }
    
    protected function createEntity() {
        return new Institution();
    }

    protected function indexFromEntity(InstitutionInterface $institution) {
        return $institution->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}