<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\NetworkInterface;


/**
 *
 * @author vlse2610
 */
interface NetworkRepoInterface extends RepoInterface  {
   
   
    /**
     * 
     * @param type $id
     * @return NetworkInterface|null
     */
    public function get( $id ): ?NetworkInterface ;
    
    
    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return NetworkInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array ;
    

    /**
     * 
     * @return NetworkInterface[]
     */
    public function findAll() :array ;
       
    
    /**
     * 
     * @param NetworkInterface $jobtag
     * @return void
     */
    public function add(NetworkInterface $jobtag ) :void ; 
    
    
    
    /**
     * 
     * @param NetworkInterface $jobtag
     * @return void
     */
    public function remove(NetworkInterface $jobtag) :void ;
}
