<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyParameterInterface extends PersistableEntityInterface {

   
    public function getCompanyId()  ;       
    
    /**
     * 
     * @return type
     */
    public function getJobLimit() ;    
    /**
     * 
     * @param type $companyId
     * @return $this
     */
    public function setCompanyId($companyId) ;       
    /**
     * 
     * @param type $jobLimit
     * @return $this
    */
    public function setJobLimit($jobLimit) ;
  
}
