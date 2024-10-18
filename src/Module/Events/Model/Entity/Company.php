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
}

