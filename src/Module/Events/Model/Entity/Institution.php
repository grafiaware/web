<?php
namespace Events\Model\Entity;

use Events\Model\Entity\InstitutionInterface;
use Model\Entity\PersistableEntityAbstract;

/**
 * Description of Institution
 *
 * @author vlse2610
 */
class Institution extends PersistableEntityAbstract implements InstitutionInterface {

    private $id;
    private $name;
    private $institutionTypeId;

    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getInstitutionTypeId()  {
        return $this->institutionTypeId;
    }



    public function setId($id): InstitutionInterface {
        $this->id = $id;
        return $this;
    }

    public function setName( $name ): InstitutionInterface {
        $this->name = $name;
        return $this;
    }

    public function setInstitutionTypeId($institutionTypeId): InstitutionInterface {
        $this->institutionTypeId = $institutionTypeId;
        return $this;

    }



}
