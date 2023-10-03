<?php

namespace Events\Model\Entity;

use Events\Model\Entity\RepresentativeInterface;
use Model\Entity\PersistableEntityAbstract;

/**
 * Description of Representative
 *
 * @author vlse2610
 */
class Representative  extends PersistableEntityAbstract implements RepresentativeInterface {

    private $companyId;         //NOT NULL
    private $loginLoginName;    //NOT NULL

    /**
     *
     * @return type
     */
    public function getCompanyId()  {
        return $this->companyId;
    }
    /**
     *
     * @return string
     */
    public function getLoginLoginName() {
        return $this->loginLoginName;
    }
    /**
     *
     * @param type $companyId
     * @return RepresentativeInterface $this
     */
    public function setCompanyId(  $companyId) : RepresentativeInterface {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param string $loginLoginName
     * @return RepresentativeInterface $this
     */
    public function setLoginLoginName(  $loginLoginName)  : RepresentativeInterface{
        $this->loginLoginName = $loginLoginName;
        return $this;
    }






}
