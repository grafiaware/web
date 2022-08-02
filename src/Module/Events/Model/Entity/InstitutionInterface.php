<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionInterface  extends EntityInterface {
   
    public function getId() ;
    
    public function getName() ;
    
    public function getInstitutionTypeId() ;
    
   
    
    
    public function setId($id): InstitutionInterface ;    
    
    public function setName($name): InstitutionInterface ;
    

    public function setInstitutionTypeId($institutionTypeId): InstitutionInterface ;
    
   
}
