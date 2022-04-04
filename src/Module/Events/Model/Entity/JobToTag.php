<?php

namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\JobToTagInterface;

/**
 * Description of JobToTag
 *
 * @author vlse2610
 */
class JobToTag extends EntityAbstract implements JobToTagInterface {
 
    private $jobId;  //NOT NULL
    private $jobTagId;   //NOT NULL
    
    private $keyAttribute = 'job_id';
      
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    
    
   /**
    * 
    * @return int
    */
    public function getJobId() : int{
        return $this->jobId;
    }
    /**
     * 
     * @return int
     */ 
    public function getJobTagId() : int{
        return $this->jobTagId;
    }
   
    /**
     * 
     * @param int $jobId
     * @return JobToTagInterface
     */
    public function setJobId( int $jobId) : JobToTagInterface {
        $this->jobId = $jobId;
        return $this;
    }            
    /**
     * 
     * @param int $jobTagId
     * @return JobToTagInterface
     */
    public function setJobTagId( int $jobTagId) : JobToTagInterface {
        $this->jobTagId = $jobTagId;
        return $this;
    }

    
}
