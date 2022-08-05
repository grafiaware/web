<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\EventLinkPhaseInterface;


/**
 *
 * @author vlse2610
 */
interface EventLinkPhaseRepoInterface  extends RepoInterface {
   
    /**
    * 
    * @param type $id
    * @return EventLinkPhaseInterface|null
    */  
    public function get($id): ?EventLinkPhaseInterface;
    
    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return EventLinkPhaseInterface[]
     */    
    public function find($whereClause=null, $touplesToBind=[]) : array ;
    
    
     /**
     * 
     * @return EventLinkPhaseInterface[]
     */
    public function findAll() : array  ;
    
    
    /**
     * 
     * @param EventLinkPhaseInterface $eventLinkPhase
     * @return void
     */
    public function add(EventLinkPhaseInterface $eventLinkPhase ) :void;
    
    
    /**
     * 
     * @param EventLinkPhaseInterface $eventLinkPhase
     * @return void
     */
    public function remove(EventLinkPhaseInterface $eventLinkPhase ) : void ;
}
