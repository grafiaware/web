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
    private $jobTagTag;   //NOT NULL

   /**
    *
    * @return int
    */
    public function getJobId() {
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
    public function setJobId(  $jobId) : JobToTagInterface {
        $this->jobId = $jobId;
        return $this;
    }

    /**
     *
     * @param string $jobTagTag
     * @return JobToTagInterface
     */
    public function setJobTagTag(  $jobTagTag) : JobToTagInterface {
        $this->jobTagTag = $jobTagTag;
        return $this;
    }


}
