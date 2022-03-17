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
    function getName(): ?string {
        return $this->name;
    }
    function getInstitutionTypeId() : ?int {
        return $this->institutionTypeId;
    }
   
    
    
    public function setId($id): InstitutionInterface {
        $this->id = $id;
        return $this;
    }
    
    function setName($name): InstitutionInterface {
        $this->name = $name;
    }

    function setInstitutionTypeId($institutionTypeId): InstitutionInterface {
        $this->institutionTypeId = $institutionTypeId;
    }
   
    
    
}
