<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyContactInterface extends EntityInterface {

    
    
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
     * @param string $id
     * @return CompanyContactInterface
     */
    public function setId(  $id) :CompanyContactInterface;   
    /**
     * 
     * @param type $company_id
     * @return CompanyContactInterface
     */
    public function setCompanyId(  $companyId) :CompanyContactInterface ;      
    /**
     * 
     * @param string $name
     * @return CompanyContactInterface
     */
    public function setName( $name=null) :CompanyContactInterface ;    
    /**
     * 
     * @param string $phones
     * @return CompanyContactInterface
     */
    public function setPhones( $phones=null) :CompanyContactInterface ;    
    /**
     * 
     * @param string $mobiles
     * @return CompanyContactInterface
     */
    public function setMobiles(  $mobiles=null) :CompanyContactInterface ;    
    /**
     * 
     * @param string $emails
     * @return CompanyContactInterface
     */
    public function setEmails( $emails=null) :CompanyContactInterface ;    
        
    
}
