<?php

namespace Events\Model\Entity;

use Events\Model\Entity\RepresentativeInterface;
use Model\Entity\EntityAbstract;

/**
 * Description of Representative
 *
 * @author vlse2610
 */
class Representative  extends EntityAbstract implements RepresentativeInterface {
    
    private $companyId;
    private $loginLoginName;

    private $keyAttribute = 'company_id';
    
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getCompanyId() : int {
        return $this->companyId;
    }
    /**
     * 
     * @return string
     */
    public function getLoginLoginName() :string {
        return $this->loginLoginName;
    }
    /**
     * 
     * @param int $companyId
     * @return RepresentativeInterface
     */
    public function setCompanyId( int $companyId) : RepresentativeInterface {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     * 
     * @param string $loginLoginName
     * @return RepresentativeInterface
     */
    public function setLoginLoginName( string $loginLoginName)  : RepresentativeInterface{
        $this->loginLoginName = $loginLoginName;
        return $this;
    }


    
    
    
    
}