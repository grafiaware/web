<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\EventContentTypeInterface;

/**
 *
 * @author vlse2610
 */
interface EventContentTypeRepoInterface extends RepoInterface  {
   
    /**
     * 
     * @param type $id
     * @return EventContentTypeInterface|null
     */
    public function get($id): ?EventContentTypeInterface ;

    /**
     * 
     * @return EventContentTypeInterface[]
     */
    public function findAll() :array ;
       
    /**
     * 
     * @param EventContentTypeInterface $eventContentType
     * @return void
     */
    public function add(EventContentTypeInterface $eventContentType) :void ; 
    
    /**
     * 
     * @param EventContentTypeInterface $eventContentType
     * @return void
     */
    public function remove(EventContentTypeInterface $eventContentType) :void ;
}
