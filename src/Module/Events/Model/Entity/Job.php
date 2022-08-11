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

    private $id;
    private $companyId;
    private $pozadovaneVzdelaniStupen;
    private $nazev;
    private $mistoVykonu;
    private $popisPozice;
    private $pozadujeme;
    private $nabizime;

    public function getId()  {
        return $this->id;
    }

    
    public function getCompanyId()  {
        return $this->companyId;
    }
    
    public function getPozadovaneVzdelaniStupen()  {
        return $this->pozadovaneVzdelaniStupen;
    }
   
    public function getNazev() {
        return $this->nazev;
    }
    
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
    public function setId($id) : JobInterface{
        $this->id = $id;
        return $this;
    }
    /**
     *
     * @param int $companyId
     * @return JobInterface
     */
    public function setCompanyId( $companyId) : JobInterface{
        $this->companyId = $companyId;
        return $this;
    }
    /**
     *
     * @param int $pozadovaneVzdelaniStupen
     * @return JobInterface
     */
    public function setPozadovaneVzdelaniStupen(  $pozadovaneVzdelaniStupen) : JobInterface{
        $this->pozadovaneVzdelaniStupen = $pozadovaneVzdelaniStupen;
        return $this;
    }
    /**
     *
     * @param string $nazev
     * @return JobInterface
     */
    public function setNazev( $nazev=null) : JobInterface{
        $this->nazev = $nazev;
        return $this;
    }
    /**
     *
     * @param string $mistoVykonu
     * @return JobInterface
     */
    public function setMistoVykonu( $mistoVykonu=null ) : JobInterface{
        $this->mistoVykonu = $mistoVykonu;
        return $this;
    }

    /**
     *
     * @param string $popisPozice
     * @return JobInterface
     */
    public function setPopisPozice( $popisPozice=null) : JobInterface{
        $this->popisPozice = $popisPozice;
        return $this;
    }
   /**
    *
    * @param string $pozadujeme
    * @return JobInterface
    */
    public function setPozadujeme( $pozadujeme=null ) : JobInterface{
        $this->pozadujeme = $pozadujeme;
        return $this;
    }
    /**
     *
     * @param string $nabizime
     * @return JobInterface
     */
    public function setNabizime( $nabizime=null) : JobInterface{
        $this->nabizime = $nabizime;
        return $this;
    }



}
