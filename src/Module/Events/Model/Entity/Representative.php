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

    private $companyId;
    private $loginLoginName;

    /**
     *
     * @return int
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
     * @param int $companyId
     * @return RepresentativeInterface
     */
    public function setCompanyId(  $companyId) : RepresentativeInterface {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param string $loginLoginName
     * @return RepresentativeInterface
     */
    public function setLoginLoginName(  $loginLoginName)  : RepresentativeInterface{
        $this->loginLoginName = $loginLoginName;
        return $this;
    }






}
