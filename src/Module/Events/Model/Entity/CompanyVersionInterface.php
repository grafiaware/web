<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\CompanyVersionInterface;

/**
 *
 * @author vlse2610
 */
interface CompanyVersionInterface extends PersistableEntityInterface {

    /**
     * 
     * @return string
     */
    public function getVersion() : string ;   
    
    /**
     * 
     * @param string $version
     * @return $this
     */
    public function setVersion($version): CompanyVersionInterface;    
}
