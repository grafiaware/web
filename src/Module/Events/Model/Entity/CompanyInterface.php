<?php
namespace Events\Model\Entity;

use Events\Model\Entity\CompanyInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyInterface   extends EntityInterface {
    /**
    * 
    * @return int
    */       
    public function getId() : int;    
    /**
     * 
     * @return string|null
     */
    public function getName() : ?string ;       
    /**
     * 
     * @return string|null
     */ 
    public function getEventInstitutionName30() :?string ;    
    /**
     * 
     * @param type $id
     * @return CompanyInterface
     */
    public function setId($id) :CompanyInterface ;
    /**
     * 
     * @param string $name
     * @return CompanyInterface
     */  
    public function setName( string $name=null) :CompanyInterface ;          
    /**
     * 
     * @param string $eventInstitutionName30
     * @return CompanyInterface
     */
    public function setEventInstitutionName30(string $eventInstitutionName30=null) :CompanyInterface ;    
      
    
    
}
