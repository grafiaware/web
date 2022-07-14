<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\EventContentTypeInterface;

/**
 *
 * @author pes2704
 */
interface EventContentTypeRepoInterface extends RepoInterface  {
   
    /**
     * 
     * @param string $type
     * @return EventContentTypeInterface|null
     */
    public function get(string $type): ?EventContentTypeInterface ;

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
