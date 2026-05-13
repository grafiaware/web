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


    #[\Override]
    public function getId()  {
        return $this->id;
    }


    #[\Override]
    public function getCompanyId()  {
        return $this->companyId;
    }
    

    #[\Override]
    public function getPublished() {
        return $this->published;
    }


    #[\Override]
    public function getPozadovaneVzdelaniStupen()  {
        return $this->pozadovaneVzdelaniStupen;
    }
    

    #[\Override]
    public function getNazev() {
        return $this->nazev;
    }
    

    #[\Override]
    public function getMistoVykonu() {
        return $this->mistoVykonu;
    }

    #[\Override]
    public function getPopisPozice() {
        return $this->popisPozice;
    }

    #[\Override]
    public function getPozadujeme() {
        return $this->pozadujeme;
    }

    #[\Override]
    public function getNabizime() {
        return $this->nabizime;
    }


    #[\Override]
    public function setId($id): JobInterface {
        $this->id = $id;
        return $this;
    }

    #[\Override]
    public function setCompanyId( $companyId): JobInterface {
        $this->companyId = $companyId;
        return $this;
    }

   
    #[\Override]
    public function setPublished($published): JobInterface {
        $this->published = $published;
        return $this;
    }


    #[\Override]
    public function setPozadovaneVzdelaniStupen(  $pozadovaneVzdelaniStupen): JobInterface {
        $this->pozadovaneVzdelaniStupen = $pozadovaneVzdelaniStupen;
        return $this;
    }

    #[\Override]
    public function setNazev( $nazev ): JobInterface {
        $this->nazev = $nazev;
        return $this;
    }

    #[\Override]
    public function setMistoVykonu( $mistoVykonu  ): JobInterface {
        $this->mistoVykonu = $mistoVykonu;
        return $this;
    }

   
    #[\Override]
    public function setPopisPozice( $popisPozice ): JobInterface {
        $this->popisPozice = $popisPozice;
        return $this;
    }

    #[\Override]
    public function setPozadujeme( $pozadujeme ): JobInterface {
        $this->pozadujeme = $pozadujeme;
        return $this;
    }

    #[\Override]
    public function setNabizime( $nabizime ): JobInterface {
        $this->nabizime = $nabizime;
        return $this;
    }



}
