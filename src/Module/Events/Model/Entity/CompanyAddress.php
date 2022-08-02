<?php
namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\CompanyAddressInterface;

/**
 * Description of CompanyAddress
 *
 * @author vlse2610
 */
class CompanyAddress extends EntityAbstract implements CompanyAddressInterface {

    private $companyId;   //NOT NULL

    private $name;      //NOT NULL
    private $lokace;    //NOT NULL
    private $psc;
    private $obec;

    
    
    public function getCompanyId()  {
        return $this->companyId;
    }
    
    public function getName() {
        return $this->name;
    }
    
    
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
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId( int $companyId) {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param string $name
     * @return CompanyAddressInterface
     */
    public function setName( string $name) :CompanyAddressInterface {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @param string $lokace
     * @return CompanyAddressInterface
     */
    public function setLokace( string $lokace) :CompanyAddressInterface {
        $this->lokace = $lokace;
        return $this;
    }
    /**
     *
     * @param string $psc
     * @return CompanyAddressInterface
     */
    public function setPsc( string $psc= null):CompanyAddressInterface {
        $this->psc = $psc;
        return $this;
    }
    /**
     *
     * @param string $obec
     * @return CompanyAddressInterface
     */
    public function setObec( string $obec= null ):CompanyAddressInterface {
        $this->obec = $obec;
        return $this;
    }


}
