<?php
namespace Events\Component\Model\Entity;

use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface JobTagInterface extends PersistableEntityInterface {
    
    
    public function getId()  ;
    
    /**
     * 
     * @return string
     */
    public function getTag() ;
        
    
     /**
     *
     * @param type $id
     * @return JobTagInterface $this
     */
    public function setId($id) : JobTagInterface;
    
    
    /**
     * 
     * @param string $tag
     * @return JobTagInterface $this
     */
    public function setTag( $tag) : JobTagInterface ;
       
    
    
}
