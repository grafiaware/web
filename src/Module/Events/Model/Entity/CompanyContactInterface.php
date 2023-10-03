<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyContactInterface extends PersistableEntityInterface {

    
    
    public function getId() ;    
    
    public function getCompanyId() ;    
    /**
     * 
     * @return string|null
     */
    public function getName()  ;  
    /**
     * 
     * @return string|null
     */
    public function getPhones()  ;    
    /**
     * 
     * @return string|null
     */
    public function getMobiles()  ;
    /**
     * 
     * @return string|null
     */
    public function getEmails()  ;    
    /**
     * 
     * @param type $id
     * @return CompanyContactInterface
     */
    public function setId( $id) :CompanyContactInterface;   
    /**
     * 
     * @param type $companyId
     * @return CompanyContactInterface
     */
    public function setCompanyId(  $companyId) :CompanyContactInterface ;      
    /**
     * 
     * @param string $name
     * @return CompanyContactInterface
     */
    public function setName( $name ) :CompanyContactInterface ;    
    /**
     * 
     * @param string $phones
     * @return CompanyContactInterface
     */
    public function setPhones( $phones ) :CompanyContactInterface ;    
    /**
     * 
     * @param string $mobiles
     * @return CompanyContactInterface
     */
    public function setMobiles(  $mobiles ) :CompanyContactInterface ;    
    /**
     * 
     * @param string $emails
     * @return CompanyContactInterface
     */
    public function setEmails( $emails ) :CompanyContactInterface ;    
        
    
}
