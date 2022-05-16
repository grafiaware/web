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
    private $jobTagTag;   //NOT NULL
    
    private $keyAttribute = ['job_id', 
                             'job_tag_tag'];
      
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
     * @return string
     */ 
    public function getJobTagTag() : string{
        return $this->jobTagTag;
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
     * @param string $jobTagTag
     * @return JobToTagInterface
     */
    public function setJobTagTag( string $jobTagTag) : JobToTagInterface {
        $this->jobTagTag = $jobTagTag;
        return $this;
    }

    
}
