<?php

namespace Events\Model\Entity;

use Events\Model\Entity\InstitutionTypeInterface;
use Model\Entity\PersistableEntityAbstract;



/**
 * Description of InstitutionType
 *
 * @author vlse2610
 */
class InstitutionType  extends PersistableEntityAbstract implements InstitutionTypeInterface {

    private $id;
    private $institutionType;  //NOT NULL

    public function getId() {
        return $this->id;
    }

    public function getInstitutionType() {
        return $this->institutionType;
    }

    public function setId($id): InstitutionTypeInterface {
        $this->id = $id;
        return $this;
    }

    public function setInstitutionType(  $value ): InstitutionTypeInterface {
        $this->institutionType = $value;
        return $this;
    }



}
