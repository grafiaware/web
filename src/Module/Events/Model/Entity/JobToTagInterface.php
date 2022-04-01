<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;


/**
 *
 * @author vlse2610
 */
interface JobToTagInterface extends EntityInterface {
    
    
   /**
    * 
    * @return int
    */
    public function getJobId() : int;
    
     /**
     * 
     * @return int
     */ 
    public function getJobTagId() : int;
        
    
    /**
     * 
     * @param int $jobId
     * @return JobToTagInterface
     */
    public function setJobId( int $jobId) : JobToTagInterface ;                    
   
   
    
    /**
     * 
     * @param int $jobTagId
     * @return JobToTagInterface
     */
    public function setJobTagId( int $jobTagId) : JobToTagInterface;
    

    
    
}
