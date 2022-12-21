<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface JobInterface extends PersistableEntityInterface {
   
     
    public function getId()  ;        
    
    public function getCompanyId()  ;
    
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
    public function setCompanyId( $companyId) : JobInterface;
    
    /**
     * 
     * @param int $pozadovaneVzdelaniStupen
     * @return JobInterface
     */
    public function setPozadovaneVzdelaniStupen(  $pozadovaneVzdelaniStupen) : JobInterface;
    
    /**
     * 
     * @param string $nazev
     * @return JobInterface
     */
    public function setNazev( $nazev=null) : JobInterface;
    
    /**
     * 
     * @param string $mistoVykonu
     * @return JobInterface
     */
    public function setMistoVykonu( $mistoVykonu=null ) : JobInterface;
    
    
    /**
     * 
     * @param string $popisPozice
     * @return JobInterface
     */
    public function setPopisPozice( $popisPozice=null) : JobInterface;
    
   /**
    * 
    * @param string $pozadujeme
    * @return JobInterface
    */
    public function setPozadujeme( $pozadujeme=null ) : JobInterface;
    
    /**
     * 
     * @param string $nabizime
     * @return JobInterface
     */
    public function setNabizime( $nabizime=null) : JobInterface;
    

}
