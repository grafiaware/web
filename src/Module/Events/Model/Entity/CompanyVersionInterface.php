<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

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
