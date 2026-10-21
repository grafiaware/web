<?php

namespace Events\Model\Entity;

use Pes\Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeInterface extends PersistableEntityInterface {

    /**
     * 
     * @return type
     */ 
    public function getCompanyId()  ;
    
    /**
     * 
     * @return string
     */
    public function getLoginLoginName() ;
    
    /**
     * 
     * @param type $companyId
     * @return RepresentativeInterface $this
     */
    public function setCompanyId(  $companyId) : RepresentativeInterface ;
    
    /**
     * 
     * @param string $loginLoginName
     * @return RepresentativeInterface $this
     */
    public function setLoginLoginName(  $loginLoginName)  : RepresentativeInterface;
    


    
    
}
