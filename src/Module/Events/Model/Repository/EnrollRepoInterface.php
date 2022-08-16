<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\EnrollInterface;




/**
 *
 * 
 */
interface EnrollRepoInterface  extends RepoInterface { 
    /**
     * 
     * @param type $loginName
     * @param type $eventId
     * @return EnrollInterface|null
     */
    public function get($loginName, $eventId): ?EnrollInterface ;
          
    /**
     * 
     * @param type $loginName     
     * @return EnrollInterface[]
     */
    public function findByLoginName($loginName) : array;
    
        
    /**
     * 
     * @param type $eventId     
     * @return EnrollInterface[]
     */
    public function findByEvent($eventId) : array;
    
        
    /**
     * 
     * @return EnrollInterface[]
     */
    public function findAll(): array  ;
    
    /**
     * 
     * @param EnrollInterface $enroll
     * @return void
     */
    public function add(EnrollInterface $enroll) :void;
    
    /**
     * 
     * @param EnrollInterface $enroll
     * @return void
     */
    public function remove(EnrollInterface $enroll)  :void;
    
    
}
