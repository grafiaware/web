<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\CompanyParameterInterface;

/**
 * Description of CompanyAddress
 *
 * @author vlse2610
 */
class CompanyParameter extends PersistableEntityAbstract implements CompanyParameterInterface {

    private $companyId;   //NOT NULL
    
    private $jobLimit;        
   
    /**
     * 
     * @return type
     */
    public function getCompanyId() {
        return $this->companyId;
    }   
    /**
     * 
     * @return type
     */
    public function getJobLimit() {
        return $this->jobLimit;
    }
    
    /**
     * 
     * @param type $companyId
     * @return $this
     */
    public function setCompanyId($companyId) {
        $this->companyId = $companyId;
        return $this;
    }  
    /**
     * 
     * @param type $jobLimit
     * @return $this
    */
    public function setJobLimit($jobLimit) {
        $this->jobLimit = $jobLimit;
        return $this;
    }
    
}
