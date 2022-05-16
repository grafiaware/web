<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;


/**
 *
 * @author vlse2610
 */
interface JobTagInterface extends EntityInterface {
  
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
    public function setTag(string $tag) : JobTagInterface ;
       
    
}
