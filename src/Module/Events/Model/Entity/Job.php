<?php

namespace Events\Model\Entity;

use Events\Model\Entity\JobInterface;
use Model\Entity\EntityAbstract;


/**
 * Description of Job
 *
 * @author vlse2610
 */
class Job extends EntityAbstract implements JobInterface {
//    `job` 
//  `id` // NULL AUTO_INCREMENT,
//  `company_id` // int NOT NULL,
//  `pozadovane_vzdelani_stupen` //int(11) NOT NULL,
//  `nazev` varchar(45) 
//  `misto_vykonu` varchar(45) 
//  `popis_pozice` varchar(1000) 
//  `pozadujeme` varchar(1000) 
//  `nabizime` varchar(1000) 
    
    private $keyAttribute = 'id';
    
    private $id;
    private $companyId;
    private $pozadovaneVzdelaniStupen; 
    private $nazev;
    private $mistoVykonu;
    private $popisPozice;
    private $pozadujeme; 
    private $nabizime;
    
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    
    
    public function getId()  {
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
     * @return int
     */
    public function getPozadovaneVzdelaniStupen() : int {
        return $this->pozadovaneVzdelaniStupen;
    }
    /**
     * 
     * @return string|null
     */
    public function getNazev(): ?string {
        return $this->nazev;
    }
    /**
     * 
     * @return string|null
     */
    public function getMistoVykonu() : ?string{
        return $this->mistoVykonu;
    }
    /**
     * 
     * @return string|null
     */
    public function getPopisPozice() : ?string{
        return $this->popisPozice;
    }
    /**
     * 
     * @return string|null
     */
    public function getPozadujeme() : ?string{
        return $this->pozadujeme;
    }
    /**
     * 
     * @return string|null
     */
    public function getNabizime(): ?string {
        return $this->nabizime;
    }

    
    /**
     * 
     * @param type $id
     * @return JobInterface
     */
    public function setId($id) : JobInterface{
        $this->id = $id;
        return $this;
    } 
    /**
     * 
     * @param int $companyId
     * @return JobInterface
     */
    public function setCompanyId(int $companyId) : JobInterface{
        $this->companyId = $companyId;
        return $this;
    }
    /**
     * 
     * @param int $pozadovaneVzdelaniStupen
     * @return JobInterface
     */
    public function setPozadovaneVzdelaniStupen( int $pozadovaneVzdelaniStupen) : JobInterface{
        $this->pozadovaneVzdelaniStupen = $pozadovaneVzdelaniStupen;
        return $this;
    }
    /**
     * 
     * @param string $nazev
     * @return JobInterface
     */
    public function setNazev(string $nazev=null) : JobInterface{
        $this->nazev = $nazev;
        return $this;
    }
    /**
     * 
     * @param string $mistoVykonu
     * @return JobInterface
     */
    public function setMistoVykonu(string $mistoVykonu=null ) : JobInterface{
        $this->mistoVykonu = $mistoVykonu;
        return $this;
    }
    
    /**
     * 
     * @param string $popisPozice
     * @return JobInterface
     */
    public function setPopisPozice(string $popisPozice=null) : JobInterface{
        $this->popisPozice = $popisPozice;
        return $this;
    }
   /**
    * 
    * @param string $pozadujeme
    * @return JobInterface
    */
    public function setPozadujeme(string $pozadujeme=null ) : JobInterface{
        $this->pozadujeme = $pozadujeme;
        return $this;
    }
    /**
     * 
     * @param string $nabizime
     * @return JobInterface
     */
    public function setNabizime(string $nabizime=null) : JobInterface{
        $this->nabizime = $nabizime;
        return $this;
    }


 
}
