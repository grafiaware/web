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
    private $published;

       
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
     * @return string
     */
    public function getPublished() {
        return $this->published;
    }
    
    /**
     *
     * @param string $companyId
     * @return CompanytoNetworkInterface $this
     */
    public function setCompanyId($companyId): CompanytoNetworkInterface {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * 
     * @param string $networkId
     * @return CompanytoNetworkInterface
     */
    public function setNetworkId($networkId): CompanytoNetworkInterface {
        $this->networkId = $networkId;
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
    

    public function setPublished($published): CompanytoNetworkInterface  {
        $this->published = $published;
        return $this;
    }    
}
