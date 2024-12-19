<?php

namespace Events\Component\Model\Entity;

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
    * @return type
    */
    public function getJobId() {
        return $this->jobId;
    }

    /**
     * 
     * @return type
     */
    public function getJobTagId()  {
        return $this->jobTagId;
    }

    /**
     *
     * @param type $jobId
     * @return JobToTagInterface $this
     */
    public function setJobId($jobId) : JobToTagInterface {
        $this->jobId = $jobId;
        return $this;
    }

    /**
     * 
     * @param type $jobTagId
     * @return JobToTagInterface
     */
    public function setJobTagId($jobTagId) : JobToTagInterface {
        $this->jobTagId = $jobTagId;
        return $this;
    }

}
