<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\CompanytoNetworkInterface;

/**
 * Description of JobToTag
 *
 * @author vlse2610
 */
class CompanytoNetwork extends PersistableEntityAbstract implements CompanytoNetworkInterface {

    private $companyId;  //NOT NULL
    private $networkId;   //NOT NULL
    private $link;

    /**
     *
     * @return string
     */
    public function getCompanyId() {
        return $this->companyId;
    }

    /**
     * 
     * @return string
     */
    public function getNetworkId()  {
        return $this->networkId;
    }
    
    /**
     * 
     * @return string
     */
    public function getLink() {
        return $this->link;
    }
    
    /**
     *
     * @param string $jobId
     * @return JobToTagInterface $this
     */
    public function setCompanyId($jobId): JobToTagInterface {
        $this->companyId = $jobId;
        return $this;
    }

    /**
     * 
     * @param string $jobTagId
     * @return JobToTagInterface
     */
    public function setNetworkId($jobTagId): JobToTagInterface {
        $this->networkId = $jobTagId;
        return $this;
    }

    /**
     * 
     * @param string $link
     * @return CompanytoNetworkInterface
     */
    public function setLink($link): CompanytoNetworkInterface {
        $this->link = $link;
        return $this;
    }
}
