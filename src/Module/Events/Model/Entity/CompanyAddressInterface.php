<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyAddressInterface extends PersistableEntityInterface {

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
    public function setCompanyId( $companyId);    
    /**
     * 
     * @param string $name
     * @return CompanyAddressInterface
     */
    public function setName( $name) :CompanyAddressInterface;    
    /**
     * 
     * @param string $lokace
     * @return CompanyAddressInterface
     */
    public function setLokace( $lokace) :CompanyAddressInterface ;    
    /**
     * 
     * @param string $psc
     * @return CompanyAddressInterface
     */
    public function setPsc(  $psc= null):CompanyAddressInterface ;
    /**
     * 
     * @param string $obec
     * @return CompanyAddressInterface
     */
    public function setObec(  $obec= null ):CompanyAddressInterface;    

   
}
