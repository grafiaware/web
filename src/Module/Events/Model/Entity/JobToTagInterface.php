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
    * @return type
    */
    public function getJobId() ;

    
    /**
     * 
     * @return type
     */
    public function getJobTagId() ;
        
    
    /**
     * 
     * @param type $jobId
     * @return JobToTagInterface $this
     */
    public function setJobId(  $jobId) : JobToTagInterface ;                    
   
           
    
     /**
     * 
     * @param int $jobTagId
     * @return JobToTagInterface $this
     */
    public function setJobTagId($jobTagId) : JobToTagInterface;
    
}
