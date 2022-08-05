<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Dao\EventLinkPhaseDao;
use Events\Model\Hydrator\EventLinkPhaseHydrator;
use Events\Model\Repository\EventLinkPhaseRepoInterface;
use Events\Model\Entity\EventLinkPhase;
use Events\Model\Entity\EventLinkPhaseInterface;



/**
 * Description of EventLinkPhaseRepo
 *
 * @author vlse2610
 */
class EventLinkPhaseRepo extends RepoAbstract implements EventLinkPhaseRepoInterface {    

    public function __construct( EventLinkPhaseDao $eventLinkPhaseDao, EventLinkPhaseHydrator $eventLinkPhaseHydrator) {
        $this->dataManager = $eventLinkPhaseDao;
        $this->registerHydrator($eventLinkPhaseHydrator);
    }
                
//
    //
    //
    
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

