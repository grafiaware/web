<?php

namespace Events\Model\Entity;

use Events\Model\Entity\InstitutionTypeInterface;
use Model\Entity\EntityAbstract;



/**
 * Description of InstitutionType
 *
 * @author vlse2610
 */
class InstitutionType  extends EntityAbstract implements InstitutionTypeInterface {
   
    private $id;
    private $institutionType;
    
    private $keyAttribute = 'id';
    
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    
    
    public function getId(): int {
        return $this->id;
    }

    public function getInstitutionType(): ?string {
        return $this->institutionType;
    }

    public function setId($id): InstitutionTypeInterface {
        $this->id = $id;
        return $this;
    }

    public function setInstitutionType( string $value=null ): InstitutionTypeInterface {
        $this->institutionType = $value;
        return $this;
    }

    
    
}
