<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface JobInterface extends PersistableEntityInterface {
   
     
    public function getId()  ;        
    
     /**
     *
     * @return string|null
     */
    public function getCompanyId();
    
    /**
     * @return string
     */
    public function getPublished();
    
    /**
     *
     * @return string
     */
    public function getPozadovaneVzdelaniStupen();
    
    /**
     * 
     * @return string|null
     */
    public function getNazev();
    
    /**
     * 
     * @return string|null
     */
    public function getMistoVykonu();
    
    /**
     * 
     * @return string|null
     */
    public function getPopisPozice();
    
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
     * @param type $companyId
     * @return JobInterface
     */
    public function setCompanyId( $companyId) : JobInterface;
    
    /**
     * 
     * @param type $published
     * @return JobInterface
     */
    public function setPublished($published): JobInterface;
    
    /**
     * 
     * @param type $pozadovaneVzdelaniStupen
     * @return JobInterface
     */
    public function setPozadovaneVzdelaniStupen(  $pozadovaneVzdelaniStupen) : JobInterface;
    
    /**
     * 
     * @param string|null $nazev
     * @return JobInterface
     */
    public function setNazev( $nazev) : JobInterface;
    
    /**
     * 
     * @param string|null $mistoVykonu
     * @return JobInterface
     */
    public function setMistoVykonu( $mistoVykonu ) : JobInterface;
    
    
    /**
     * 
     * @param string|null $popisPozice
     * @return JobInterface
     */
    public function setPopisPozice( $popisPozice ) : JobInterface;
    
   /**
    * 
    * @param string|null $pozadujeme
    * @return JobInterface
    */
    public function setPozadujeme( $pozadujeme ) : JobInterface;
    
    /**
     * 
     * @param string|null $nabizime
     * @return JobInterface
     */
    public function setNabizime( $nabizime ) : JobInterface;
    

}
