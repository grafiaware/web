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
    * @return int
    */       
    public function getId() : int;    
            
    /**
     * 
     * @return string|null
     */
    public function getTag() : ?string ;
        
    /**
     * 
     * @param type $id
     * @return JobTagInterface
     */
    public function setId($id) : JobTagInterface ;
    
    /**
     * 
     * @param string $tag
     * @return JobTagInterface
     */
    public function setTag(string $tag=null) : JobTagInterface ;
       
    
}
