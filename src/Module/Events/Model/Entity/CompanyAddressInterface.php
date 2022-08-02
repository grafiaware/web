<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyAddressInterface extends EntityInterface {

    /**
     * 
     * @return int
     */
    public function getCompanyId()  ;   
    /**
     * 
     * @return string
     */
    public function getName() ;   
    /**
     * 
     * @return string
     */
    public function getLokace()  ;   
    /**
     * 
     * @return string|null
     */
    public function getPsc()   ;    
    /**
     * 
     * @return string|null
     */
    public function getObec()  ;    
    /**
     * 
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId( int $companyId);    
    /**
     * 
     * @param string $name
     * @return CompanyAddressInterface
     */
    public function setName( string $name) :CompanyAddressInterface;    
    /**
     * 
     * @param string $lokace
     * @return CompanyAddressInterface
     */
    public function setLokace( string $lokace) :CompanyAddressInterface ;    
    /**
     * 
     * @param string $psc
     * @return CompanyAddressInterface
     */
    public function setPsc( string $psc= null):CompanyAddressInterface ;
    /**
     * 
     * @param string $obec
     * @return CompanyAddressInterface
     */
    public function setObec( string $obec= null ):CompanyAddressInterface;    

   
}
