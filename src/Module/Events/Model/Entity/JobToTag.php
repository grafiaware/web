<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\JobToTagInterface;

/**
 * Description of JobToTag
 *
 * @author vlse2610
 */
class JobToTag extends PersistableEntityAbstract implements JobToTagInterface {

    private $jobId;  //NOT NULL
    private $jobTagId;   //NOT NULL

   /**
    *
    * @return int
    */
    public function getJobId() {
        return $this->jobId;
    }

    /**
     * 
     * @return int
     */
    public function getJobTagId() :int {
        return $this->jobTagId;
    }

    

    
    /**
     *
     * @param int $jobId
     * @return JobToTagInterface
     */
    public function setJobId($jobId) : JobToTagInterface {
        $this->jobId = $jobId;
        return $this;
    }


    
    /**
     * 
     * @param int $jobTagId
     * @return JobToTagInterface
     */
    public function setJobTagId($jobTagId) : JobToTagInterface {
        $this->jobTagId = $jobTagId;
        return $this;
    }

}
