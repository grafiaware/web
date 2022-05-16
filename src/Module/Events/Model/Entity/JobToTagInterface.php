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
     * @return string
     */ 
    public function getJobTagTag() : string;
        
    
    /**
     * 
     * @param int $jobId
     * @return JobToTagInterface
     */
    public function setJobId( int $jobId) : JobToTagInterface ;                    
   
           
    /**
     * 
     * @param string $jobTagTag
     * @return JobToTagInterface
     */
    public function setJobTagTag( string $jobTagTag) : JobToTagInterface;
    

    
    
}
