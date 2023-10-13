<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\CompanyInterface;

/**
 * Description of Company
 *
 * @author vlse2610
 */
class Company  extends PersistableEntityAbstract implements CompanyInterface {

    private $id; //NOT NULL
    
    private $name;
    private $eventInstitutionName30;

   
    public function getId()  {
        return $this->id;
    }
    /**
     *
     * @return string|null
     */
    public function getName()  {
        return $this->name;
    }
    /**
     *
     * @return string|null
     */
    public function getEventInstitutionName30()  {
        return $this->eventInstitutionName30;
    }
    /**
     *
     * @param type $id
     * @return CompanyInterface
     */
    public function setId( $id ) :CompanyInterface {
        $this->id = $id;
        return $this;
    }
    /**
     *
     * @param string $name
     * @return CompanyInterface
     */
    public function setName(  $name ) :CompanyInterface {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @param string $eventInstitutionName30
     * @return CompanyInterface
     */
    public function setEventInstitutionName30(  $eventInstitutionName30 ) :CompanyInterface {
        $this->eventInstitutionName30 = $eventInstitutionName30;
        return $this;
    }

}

