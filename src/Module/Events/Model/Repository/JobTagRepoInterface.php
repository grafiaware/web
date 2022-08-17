<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\JobTagInterface;


/**
 *
 * @author vlse2610
 */
interface JobTagRepoInterface extends RepoInterface  {
   
   
    /**
     * 
     * @param type $tag
     * @return JobTagInterface|null
     */
    public function get( $tag ): ?JobTagInterface ;

    /**
     * 
     * @return JobTagInterface[]
     */
    public function findAll() :array ;
       
    
    /**
     * 
     * @param JobTagInterface $jobtag
     * @return void
     */
    public function add(JobTagInterface $jobtag ) :void ; 
    
    
    
    /**
     * 
     * @param JobTagInterface $jobtag
     * @return void
     */
    public function remove(JobTagInterface $jobtag) :void ;
}
