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
    public function getCompanyId() : int;
    
    /**
     * 
     * @param int $jobId
     * @return JobToTagInterface
     */
    public function setJobId(int $jobId) : JobToTagInterface ;
    
    /**
     * 
     * @param int $companyId
     * @return JobToTagInterface
     */
    public function setCompanyId(int $companyId) : JobToTagInterface ;
    
    
    
}
