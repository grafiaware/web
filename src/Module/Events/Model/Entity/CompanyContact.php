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
     * @param type $id
     * @return CompanyContactInterface $this
     */
    public function setId( $id) :CompanyContactInterface{
        $this->id = $id;
        return $this;
    }
    /**
     *
     * @param type $companyId
     * @return CompanyContactInterface $this
     */
    public function setCompanyId( $companyId ) :CompanyContactInterface {
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param string|null $name
     * @return CompanyContactInterface $this
     */
    public function setName( $name ) :CompanyContactInterface {
        $this->name = $name;
        return $this;
    }
    /**
     *
     * @param string|null $phones
     * @return CompanyContactInterface
     */
    public function setPhones( $phones) :CompanyContactInterface {
        $this->phones = $phones;
        return $this;
    }
    /**
     *
     * @param string|null $mobiles
     * @return CompanyContactInterface
     */
    public function setMobiles(  $mobiles ) :CompanyContactInterface {
        $this->mobiles = $mobiles;
        return $this;
    }
    /**
     *
     * @param string|null $emails
     * @return CompanyContactInterface
     */
    public function setEmails( $emails ) :CompanyContactInterface {
        $this->emails = $emails;
        return $this;
    }

}
