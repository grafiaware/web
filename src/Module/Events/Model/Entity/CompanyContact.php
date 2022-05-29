<?php
namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\CompanyContactInterface;

/**
 * Description of CompanyContact
 *
 * @author vlse2610
 */
class CompanyContact extends EntityAbstract implements CompanyContactInterface {

    private $id;  //NOT NULL
    private $companyId;   //NOT NULL

    private $name;
    private $phones;
    private $mobiles;
    private $emails;

    /**
     *
     * @return int
     */
    public function getId() : int {
        return $this->id;
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
     * @return string|null
     */
    public function getName() :?string {
        return $this->name;
    }
    /**
     *
     * @return string|null
     */
    public function getPhones() :?string {
        return $this->phones;
    }
    /**
     *
     * @return string|null
     */
    public function getMobiles() :?string {
        return $this->mobiles;
    }
    /**
     *
     * @return string|null
     */
    public function getEmails() :?string {
        return $this->emails;
    }
    /**
     *
     * @param type $id
     * @return CompanyContactInterface
     */
    public function setId(  $id) :CompanyContactInterface{
        $this->id = $id;
        return $this;
    }
    /**
     *
     * @param int $company_id
     * @return CompanyContactInterface
     */
    public function setCompanyId( int $companyId) :CompanyContactInterface {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param string $name
     * @return CompanyContactInterface
     */
    public function setName(string $name=null) :CompanyContactInterface {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @param string $phones
     * @return CompanyContactInterface
     */
    public function setPhones(string $phones=null) :CompanyContactInterface {
        $this->phones = $phones;
        return $this;
    }
    /**
     *
     * @param string $mobiles
     * @return CompanyContactInterface
     */
    public function setMobiles( string $mobiles=null) :CompanyContactInterface {
        $this->mobiles = $mobiles;
        return $this;
    }
    /**
     *
     * @param string $emails
     * @return CompanyContactInterface
     */
    public function setEmails(string $emails=null) :CompanyContactInterface {
        $this->emails = $emails;
        return $this;
    }

}
