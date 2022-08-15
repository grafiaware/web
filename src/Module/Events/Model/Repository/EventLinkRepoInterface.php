<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\EventLinkInterface;


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
