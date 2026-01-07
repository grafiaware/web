<?php
namespace Events\Model\Entity;

use Events\Model\Entity\CompanyInterface;
use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface CompanyInterface   extends PersistableEntityInterface {
    
    /**
     * 
     * @return string
     */
    public function getId();   
    
    /**
     * 
     * @return string|null
     */
    public function getName();
    
    /**
     * 
     * @return string
     */
    public function getVersionFk();
     
    /**
     * 
     * @param type $id
     * @return CompanyInterface
     */
    public function setId($id): CompanyInterface;
    
    /**
     * 
     * @param string $name
     * @return CompanyInterface
     */  
    public function setName(  $name ): CompanyInterface;
    
    /**
     * 
     * @param string $versionFk
     * @return CompanyInterface
     */
    public function setVersionFk($versionFk): CompanyInterface;    
}
