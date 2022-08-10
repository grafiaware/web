<?php
namespace Events\Model\Entity;

use Events\Model\Entity\CompanyInterface;
use Model\Entity\EntityInterface;


/**
 *
 * @author vlse2610
 */
interface CompanyInterface   extends EntityInterface {
    /**
    * 
    * @return int
    */       
    public function getId() ;    
    /**
     * 
     * @return string|null
     */
    public function getName()  ;       
    /**
     * 
     * @return string|null
     */ 
    public function getEventInstitutionName30()  ;    
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
