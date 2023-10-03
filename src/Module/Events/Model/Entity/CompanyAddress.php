<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\CompanyAddressInterface;

/**
 * Description of CompanyAddress
 *
 * @author vlse2610
 */
class CompanyAddress extends PersistableEntityAbstract implements CompanyAddressInterface {

    private $companyId;   //NOT NULL
    
    private $name;      //NOT NULL
    private $lokace;    //NOT NULL
    private $psc;
    private $obec;

    
    /**
     * 
     * @return integer
     */
    public function getCompanyId()  {
        return $this->companyId;
    }
    
    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * 
     * @return string
     */
    public function getLokace()  {
        return $this->lokace;
    }
    
    /**
     *
     * @return string|null
     */
    public function getPsc()   {
        return $this->psc;
    }
    
    /**
     *
     * @return string|null
     */
    public function getObec() {
        return $this->obec;
    }
    
    /**
     *
     * @param type $companyId
     * @return  CompanyAddressInterface $this
     */
    
    public function setCompanyId( $companyId) {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param string $name
     * @return CompanyAddressInterface $this
     */
    public function setName( $name) :CompanyAddressInterface {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @param string $lokace
     * @return CompanyAddressInterface $this
     */
    public function setLokace( $lokace) :CompanyAddressInterface {
        $this->lokace = $lokace;
        return $this;
    }
    /**
     *
     * @param string $psc
     * @return CompanyAddressInterface
     */
    public function setPsc( $psc ):CompanyAddressInterface {
        $this->psc = $psc;
        return $this;
    }
    /**
     *
     * @param string $obec
     * @return CompanyAddressInterface
     */
    public function setObec(  $obec ):CompanyAddressInterface {
        $this->obec = $obec;
        return $this;
    }


}
