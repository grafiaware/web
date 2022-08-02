<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface JobInterface extends EntityInterface {
   
     
    public function getId()  ;        
    /**
     * 
     * @return int
     */
    public function getCompanyId()  ;
    
    /**
     * 
     * @return int
     */
    public function getPozadovaneVzdelaniStupen()  ;
    
    /**
     * 
     * @return string|null
     */
    public function getNazev() ;
    
    /**
     * 
     * @return string|null
     */
    public function getMistoVykonu() ;
    
    /**
     * 
     * @return string|null
     */
    public function getPopisPozice() ;
    
    /**
     * 
     * @return string|null
     */
    public function getPozadujeme() ;
    
    /**
     * 
     * @return string|null
     */
    public function getNabizime();        
    /**
     * 
     * @param type $id
     * @return JobInterface
     */
    public function setId($id) : JobInterface;
    
    /**
     * 
     * @param int $companyId
     * @return JobInterface
     */
    public function setCompanyId(int $companyId) : JobInterface;
    
    /**
     * 
     * @param int $pozadovaneVzdelaniStupen
     * @return JobInterface
     */
    public function setPozadovaneVzdelaniStupen( int $pozadovaneVzdelaniStupen) : JobInterface;
    
    /**
     * 
     * @param string $nazev
     * @return JobInterface
     */
    public function setNazev(string $nazev=null) : JobInterface;
    
    /**
     * 
     * @param string $mistoVykonu
     * @return JobInterface
     */
    public function setMistoVykonu(string $mistoVykonu=null ) : JobInterface;
    
    
    /**
     * 
     * @param string $popisPozice
     * @return JobInterface
     */
    public function setPopisPozice(string $popisPozice=null) : JobInterface;
    
   /**
    * 
    * @param string $pozadujeme
    * @return JobInterface
    */
    public function setPozadujeme(string $pozadujeme=null ) : JobInterface;
    
    /**
     * 
     * @param string $nabizime
     * @return JobInterface
     */
    public function setNabizime(string $nabizime=null) : JobInterface;
    

}
