<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyContactInterface extends EntityInterface {

    
    /**
     * 
     * @return int
     */
    public function getId() : int;    
    /**
     * 
     * @return int
     */
    public function getCompanyId() : int;    
    /**
     * 
     * @return string|null
     */
    public function getName() :?string ;  
    /**
     * 
     * @return string|null
     */
    public function getPhones() :?string ;    
    /**
     * 
     * @return string|null
     */
    public function getMobiles() :?string ;
    /**
     * 
     * @return string|null
     */
    public function getEmails() :?string ;    
    /**
     * 
     * @param type $id
     * @return CompanyContactInterface
     */
    public function setId(  $id) :CompanyContactInterface;   
    /**
     * 
     * @param int $company_id
     * @return CompanyContactInterface
     */
    public function setCompanyId( int $companyId) :CompanyContactInterface ;      
    /**
     * 
     * @param string $name
     * @return CompanyContactInterface
     */
    public function setName(string $name=null) :CompanyContactInterface ;    
    /**
     * 
     * @param string $phones
     * @return CompanyContactInterface
     */
    public function setPhones(string $phones=null) :CompanyContactInterface ;    
    /**
     * 
     * @param string $mobiles
     * @return CompanyContactInterface
     */
    public function setMobiles( string $mobiles=null) :CompanyContactInterface ;    
    /**
     * 
     * @param string $emails
     * @return CompanyContactInterface
     */
    public function setEmails(string $emails=null) :CompanyContactInterface ;    
        
    
}