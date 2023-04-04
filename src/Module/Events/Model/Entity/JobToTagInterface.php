<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface JobToTagInterface extends PersistableEntityInterface {
    
    
   /**
    * 
    * @return int
    */
    public function getJobId() ;

    
    /**
     * 
     * @return int
     */
    public function getJobTagId() : int ;
        
    
    /**
     * 
     * @param int $jobId
     * @return JobToTagInterface
     */
    public function setJobId(  $jobId) : JobToTagInterface ;                    
   
           
    
     /**
     * 
     * @param int $jobTagId
     * @return JobToTagInterface
     */
    public function setJobTagId($jobTagId) : JobToTagInterface;
    
}
