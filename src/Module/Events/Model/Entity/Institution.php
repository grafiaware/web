<?php
namespace Events\Model\Entity;

use Events\Model\Entity\InstitutionInterface;
use Model\Entity\EntityAbstract;

/**
 * Description of Institution
 *
 * @author vlse2610
 */
class Institution extends EntityAbstract implements InstitutionInterface {
    
    private $id;
    private $name;
    private $institutionTypeId;

    private $keyAttribute = 'id';    

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

   
    public function getId(): ?int {
        return $this->id;
    }    
    public function getName(): ?string {
        return $this->name;
    }
    public function getInstitutionTypeId() : ?int {
        return $this->institutionTypeId;
    }
   
    
    
    public function setId($id): InstitutionInterface {
        $this->id = $id;
        return $this;
    }
    
    public function setName( $name ): InstitutionInterface {
        $this->name = $name;
    }

    public function setInstitutionTypeId($institutionTypeId): InstitutionInterface {
        $this->institutionTypeId = $institutionTypeId;
    }
   
    
    
}
