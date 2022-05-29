<?php
namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\CompanyInterface;

/**
 * Description of Company
 *
 * @author vlse2610
 */
class Company  extends EntityAbstract implements CompanyInterface {

    private $id;
    private $name;
    private $eventInstitutionName30;

    /**
     *
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }
    /**
     *
     * @return string|null
     */
    public function getName() : ?string {
        return $this->name;
    }
    /**
     *
     * @return string|null
     */
    public function getEventInstitutionName30() : ?string {
        return $this->eventInstitutionName30;
    }
    /**
     *
     * @param type $id
     * @return CompanyInterface
     */
    public function setId($id) :CompanyInterface {
        $this->id = $id;
        return $this;
    }
    /**
     *
     * @param string $name
     * @return CompanyInterface
     */
    public function setName( string $name=null) :CompanyInterface {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @param string $eventInstitutionName30
     * @return CompanyInterface
     */
    public function setEventInstitutionName30( string $eventInstitutionName30=null) :CompanyInterface {
        $this->eventInstitutionName30 = $eventInstitutionName30;
        return $this;
    }

}

