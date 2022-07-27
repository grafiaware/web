<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\EventContentInterface;



/**
 *
 * @author vlse2610
 */
interface EventContentRepoInterface extends RepoInterface  {
      
    /**
     *
     * @param string $id
     * @return EventContentInterface|null
     */
    public function get($id): ?EventContentInterface ;

    /**
     * 
     * @return EventContentInterface[]
     */
    public function findAll() :array ;
       
    
    /**
     * 
     * @param EventContentInterface $eventContent
     * @return void
     */
    public function add(EventContentInterface $eventContent) :void ; 
    
    /**
     * 
     * @param EventContentInterface $eventContent
     * @return void
     */
    public function remove(EventContentInterface $eventContent) :void ;
}
