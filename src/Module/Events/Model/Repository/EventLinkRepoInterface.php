<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\EventLinkInterface;

use Model\Repository\RepoAbstract;

use Events\Model\Dao\EventLinkPhaseDao;
use Events\Model\Hydrator\EventLinkPhaseHydrator;
use Events\Model\Repository\EventLinkPhaseRepoInterface;
use Events\Model\Entity\EventLinkPhase;
use Events\Model\Entity\EventLinkPhaseInterface;

/**
 *
 * @author pes2704
 */
interface EventLinkRepoInterface extends RepoInterface  {
  /**
     *
     * @param type $id
     * @return EventLinkInterface|null
     */
    public function get($id): ?EventLinkInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return EventLinkInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return EventLinkInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param EventLinkInterface $eventLink 
     * @return void
     */
    public function add(EventLinkInterface $eventLink) : void ;


    /**
     * 
     * @param EventLinkInterface $eventLink 
     * @return void
     */
    public function remove(EventLinkInterface $eventLink)  : void ;

}
