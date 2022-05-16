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
    public function getCompanyId() : int ;
    
    /**
     * 
     * @return string
     */
    public function getLoginLoginName() :string ;
    
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
