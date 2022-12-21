<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\RepresentativeInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeInterface extends PersistableEntityInterface {

    /**
     * 
     * @return int
     */ 
    public function getCompanyId()  ;
    
    /**
     * 
     * @return string
     */
    public function getLoginLoginName() ;
    
    /**
     * 
     * @param int $companyId
     * @return RepresentativeInterface
     */
    public function setCompanyId(  $companyId) : RepresentativeInterface ;
    
    /**
     * 
     * @param string $loginLoginName
     * @return RepresentativeInterface
     */
    public function setLoginLoginName(  $loginLoginName)  : RepresentativeInterface;
    


    
    
}
