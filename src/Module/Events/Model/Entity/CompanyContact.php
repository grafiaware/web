<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\CompanyContactInterface;

/**
 * Description of CompanyContact
 *
 * @author vlse2610
 */
class CompanyContact extends PersistableEntityAbstract implements CompanyContactInterface {

    private $id;  //NOT NULL
    private $companyId;   //NOT NULL

    private $name;
    private $phones;
    private $mobiles;
    private $emails;

    
    public function getId()  {
        return $this->id;
    }
    
    
    public function getCompanyId()  {
        return $this->companyId;
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
    public function getPhones()  {
        return $this->phones;
    }
    /**
     *
     * @return string|null
     */
    public function getMobiles()  {
        return $this->mobiles;
    }
    /**
     *
     * @return string|null
     */
    public function getEmails()  {
        return $this->emails;
    }
    /**
     *
     * @param string $id
     * @return CompanyContactInterface
     */
    public function setId( $id) :CompanyContactInterface{
        $this->id = $id;
        return $this;
    }
    /**
     *
     * @param type $company_id
     * @return CompanyContactInterface
     */
    public function setCompanyId( $companyId ) :CompanyContactInterface {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param string $name
     * @return CompanyContactInterface
     */
    public function setName( $name=null) :CompanyContactInterface {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @param string $phones
     * @return CompanyContactInterface
     */
    public function setPhones( $phones=null) :CompanyContactInterface {
        $this->phones = $phones;
        return $this;
    }
    /**
     *
     * @param string $mobiles
     * @return CompanyContactInterface
     */
    public function setMobiles(  $mobiles=null) :CompanyContactInterface {
        $this->mobiles = $mobiles;
        return $this;
    }
    /**
     *
     * @param string $emails
     * @return CompanyContactInterface
     */
    public function setEmails( $emails=null) :CompanyContactInterface {
        $this->emails = $emails;
        return $this;
    }

}
