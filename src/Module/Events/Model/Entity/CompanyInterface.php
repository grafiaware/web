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
    public function getId(): string;   
    
    /**
     * 
     * @return string|null
     */
    public function getName(): ?string;
    
    /**
     * 
     * @return string
     */
    public function getVersionFk(): string;
     
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
