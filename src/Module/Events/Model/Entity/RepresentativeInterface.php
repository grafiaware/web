<?php

namespace Events\Model\Entity;

use Model\Entity\EntityInterface;
use Events\Model\Entity\RepresentativeInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeInterface extends EntityInterface {

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
    public function setCompanyId( int $companyId) : RepresentativeInterface ;
    
    /**
     * 
     * @param string $loginLoginName
     * @return RepresentativeInterface
     */
    public function setLoginLoginName( string $loginLoginName)  : RepresentativeInterface;
    


    
    
}
