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
     */
    public function getId();   
    
    /**
     * 
     * @return string|null
     */
    public function getName();       
 
    /**
     * 
     * @param type $id
     * @return CompanyInterface
     */
    public function setId($id) :CompanyInterface;
    
    /**
     * 
     * @param string $name
     * @return CompanyInterface
     */  
    public function setName(  $name ) :CompanyInterface;
}
