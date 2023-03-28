<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface JobTagInterface extends PersistableEntityInterface {
  
    /**
     * 
     * @return string
     */
    public function getTag() : string ;
        
    
    
    /**
     * 
     * @param string $tag
     * @return JobTagInterface
     */
    public function setTag( $tag) : JobTagInterface ;
       
    
}
