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
     * @return string
     */ 
    public function getJobTagTag() : string;
        
    
    /**
     * 
     * @param int $jobId
     * @return JobToTagInterface
     */
    public function setJobId(  $jobId) : JobToTagInterface ;                    
   
           
    /**
     * 
     * @param string $jobTagTag
     * @return JobToTagInterface
     */
    public function setJobTagTag(  $jobTagTag) : JobToTagInterface;
    

    
    
}
