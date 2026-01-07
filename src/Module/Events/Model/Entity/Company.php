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
   
    private $versionFk; //NOT NULL
    
    /**
     * 
     * @return string
     */
    public function getId() {
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
     * @return string
     */
    public function getVersionFk() {
        return $this->versionFk;
    }

    /**
     *
     * @param string $id
     * @return CompanyInterface
     */
    public function setId($id): CompanyInterface {
        $this->id = $id;
        return $this;
    }
    
    /**
     *
     * @param string $name
     * @return CompanyInterface
     */
    public function setName($name): CompanyInterface {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param string $versionFk
     * @return CompanyInterface
     */
    public function setVersionFk($versionFk): CompanyInterface {
        $this->versionFk = $versionFk;
        return $this;
    }    
}

