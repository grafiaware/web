<?php

namespace Events\Model\Entity;

use Events\Model\Entity\JobInterface;
use Model\Entity\PersistableEntityAbstract;


/**
 * Description of Job
 *
 * @author vlse2610
 */
class Job extends PersistableEntityAbstract implements JobInterface {

    private $id;            //NOT NULL
    private $companyId;     //NOT NULL
    private $published;     // NOT NULL default 0
    private $pozadovaneVzdelaniStupen;  //NOT NULL
    private $nazev;
    private $mistoVykonu;
    private $popisPozice;
    private $pozadujeme;
    private $nabizime;

    /**
     * 
     * @return string|null
     */
    public function getId()  {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getCompanyId()  {
        return $this->companyId;
    }
    
    /**
     * 
     * @return string
     */
    public function getPublished() {
        return $this->published;
    }

    /**
     *
     * @return string
     */
    public function getPozadovaneVzdelaniStupen()  {
        return $this->pozadovaneVzdelaniStupen;
    }
    
    /**
     *
     * @return string|null
     */
    public function getNazev() {
        return $this->nazev;
    }
    
    /**
     *
     * @return string|null
     */
    public function getMistoVykonu() {
        return $this->mistoVykonu;
    }
    /**
     *
     * @return string|null
     */
    public function getPopisPozice() {
        return $this->popisPozice;
    }
    /**
     *
     * @return string|null
     */
    public function getPozadujeme() {
        return $this->pozadujeme;
    }
    /**
     *
     * @return string|null
     */
    public function getNabizime() {
        return $this->nabizime;
    }


    /**
     *
     * @param type $id
     * @return JobInterface
     */
    public function setId($id): JobInterface {
        $this->id = $id;
        return $this;
    }
    /**
     *
     * @param type $companyId
     * @return JobInterface
     */
    public function setCompanyId( $companyId): JobInterface {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * 
     * @param type $published
     * @return JobInterface
     */
    public function setPublished($published): JobInterface {
        $this->published = $published;
        return $this;
    }

    /**
     *
     * @param type $pozadovaneVzdelaniStupen
     * @return JobInterface
     */
    public function setPozadovaneVzdelaniStupen(  $pozadovaneVzdelaniStupen): JobInterface {
        $this->pozadovaneVzdelaniStupen = $pozadovaneVzdelaniStupen;
        return $this;
    }
    /**
     *
     * @param string|null $nazev
     * @return JobInterface
     */
    public function setNazev( $nazev ): JobInterface {
        $this->nazev = $nazev;
        return $this;
    }
    /**
     *
     * @param string|null $mistoVykonu
     * @return JobInterface
     */
    public function setMistoVykonu( $mistoVykonu  ): JobInterface {
        $this->mistoVykonu = $mistoVykonu;
        return $this;
    }

    /**
     *
     * @param string|null $popisPozice
     * @return JobInterface
     */
    public function setPopisPozice( $popisPozice ): JobInterface {
        $this->popisPozice = $popisPozice;
        return $this;
    }
   /**
    *
    * @param string|null $pozadujeme
    * @return JobInterface
    */
    public function setPozadujeme( $pozadujeme ): JobInterface {
        $this->pozadujeme = $pozadujeme;
        return $this;
    }
    /**
     *
     * @param string|null $nabizime
     * @return JobInterface
     */
    public function setNabizime( $nabizime ): JobInterface {
        $this->nabizime = $nabizime;
        return $this;
    }



}
