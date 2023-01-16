<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionInterface  extends PersistableEntityInterface {
   
    public function getId() ;
    
    public function getName() ;
    
    public function getInstitutionTypeId() ;
    
   
    
    
    public function setId($id): InstitutionInterface ;    
    
    public function setName($name): InstitutionInterface ;
    

    public function setInstitutionTypeId($institutionTypeId): InstitutionInterface ;
    
   
}
